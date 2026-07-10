<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AttendanceLogsController extends Controller
{
    /**
     * Isolated query builder helper to keep filtering and aggregation identical 
     * across index analytics, pagination, and file exports.
     */
    private function buildAttendanceQuery(Request $request)
    {
        // 1. Create a baseline subquery to find the absolute FIRST log ID per user per day
        $firstLogIdsQuery = DB::table('attendance_logs')
            ->select(DB::raw('MIN(id) as first_id'))
            ->groupBy('user_id', DB::raw('DATE(checkin_at)'));

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfDay();

        $firstLogIdsQuery->whereBetween('checkin_at', [$startDate, $endDate]);

        // 2. Build primary query, linking first log attributes and computing conditional tracking states
        $query = AttendanceLog::with(['user:id,name,email,role_id'])
            ->joinSub($firstLogIdsQuery, 'first_logs', function ($join) {
                $join->on('attendance_logs.id', '=', 'first_logs.first_id');
            })
            ->select(
                'attendance_logs.user_id',
                DB::raw('DATE(attendance_logs.checkin_at) as log_date'),
                'attendance_logs.id as id',
                'attendance_logs.email as email',
                'attendance_logs.device_type as device_type',
                'attendance_logs.ip_address as ip_address',
                'attendance_logs.checkin_at as checkin_at', // Real absolute first check-in timestamp
                
                // --- CUSTOM STATUS STYLE PIPELINE ---
                // Get the status of the absolute LATEST clock-in cycle of the day
                DB::raw("(
                    SELECT inner_logs.status 
                    FROM attendance_logs as inner_logs 
                    WHERE inner_logs.user_id = attendance_logs.user_id 
                    AND DATE(inner_logs.checkin_at) = DATE(attendance_logs.checkin_at)
                    ORDER BY inner_logs.checkin_at DESC LIMIT 1
                ) as status"),

                // --- CUSTOM CHECKOUT STYLE PIPELINE ---
                // If the latest cycle is 'active' (no checkout), return NULL. 
                // Otherwise, return the absolute final checkout timestamp of the day.
                DB::raw("(
                    SELECT CASE 
                        WHEN (SELECT inner_logs.status FROM attendance_logs as inner_logs WHERE inner_logs.user_id = attendance_logs.user_id AND DATE(inner_logs.checkin_at) = DATE(attendance_logs.checkin_at) ORDER BY inner_logs.checkin_at DESC LIMIT 1) = 'active' 
                        THEN NULL 
                        ELSE (SELECT MAX(inner_logs.checkout_at) FROM attendance_logs as inner_logs WHERE inner_logs.user_id = attendance_logs.user_id AND DATE(inner_logs.checkin_at) = DATE(attendance_logs.checkin_at))
                    END
                ) as checkout_at"),

                // Sum up cumulative session seconds across all completed cycles throughout the day
                DB::raw("(
                    SELECT SUM(inner_logs.session_duration) 
                    FROM attendance_logs as inner_logs 
                    WHERE inner_logs.user_id = attendance_logs.user_id 
                    AND DATE(inner_logs.checkin_at) = DATE(attendance_logs.checkin_at)
                ) as session_duration")
            );

        // Filter by User Name
        if ($request->filled('name')) {
            $query->whereHas('user', function ($userQuery) use ($request) {
                $userQuery->where('name', 'LIKE', "%{$request->name}%");
            });
        }

        // Global Fuzzy Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($macroQuery) use ($search) {
                $macroQuery->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%");
                })->orWhere(function ($inner) use ($search) {
                    $inner->where('attendance_logs.email', 'LIKE', "%{$search}%")
                          ->orWhere('attendance_logs.checkin_address', 'LIKE', "%{$search}%")
                          ->orWhere('attendance_logs.checkout_address', 'LIKE', "%{$search}%")
                          ->orWhere('attendance_logs.device_type', 'LIKE', "%{$search}%");
                });
            });
        }

        return $query;
    }

    /**
     * Fetch a collapsed list of daily unique attendance logs (First Check-in / Conditional Last Check-out)
     * GET /api/v1/attendance
     */
    public function index(Request $request)
    {
        $query = $this->buildAttendanceQuery($request);

        // Calculate Summary Metrics Block
        $summaryMetrics = DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query->getQuery())
            ->selectRaw("
                COUNT(*) as total_logs,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_sessions,
                SUM(CASE WHEN status != 'active' OR status IS NULL THEN 1 ELSE 0 END) as completed_sessions,
                IFNULL(SUM(session_duration), 0) as total_duration_seconds
            ")->first();

        $totalSec = (int)$summaryMetrics->total_duration_seconds;
        $hours = intdiv($totalSec, 3600);
        $minutes = intdiv($totalSec % 3600, 60);

        $summaryBlock = [
            'total_logs' => (int)$summaryMetrics->total_logs,
            'active_sessions' => (int)$summaryMetrics->active_sessions,
            'completed_sessions' => (int)$summaryMetrics->completed_sessions,
            'total_hours_logged' => "{$hours}h {$minutes}m",
        ];

        // Process File Generation Hash Pipelines
        $filterHash = md5(json_encode($request->only(['name', 'start_date', 'end_date', 'search'])));
        $fileName = "attendance_summary_export_{$filterHash}.csv";
        Storage::disk('public')->makeDirectory('exports');

        if (!Storage::disk('public')->exists("exports/{$fileName}")) {
            $logs = $this->buildAttendanceQuery($request)->orderBy('attendance_logs.checkin_at', 'desc')->get();
            $tempFile = fopen('php://temp', 'r+');
            fprintf($tempFile, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($tempFile, ['Log Date', 'Employee Name', 'Email Address', 'Status', 'First Check-In Time', 'Last Check-Out Time', 'Total Duration (Seconds)', 'IP Address', 'Device Type']);
            foreach ($logs as $log) {
                fputcsv($tempFile, [$log->log_date, $log->user?->name ?? 'N/A', $log->email, ucfirst($log->status), $log->checkin_at, $log->checkout_at ?? '—', $log->session_duration, $log->ip_address, $log->device_type]);
            }
            rewind($tempFile);
            Storage::disk('public')->put("exports/{$fileName}", stream_get_contents($tempFile));
            fclose($tempFile);
        }

        $downloadUrl = asset("storage/exports/{$fileName}");

        // Order and Paginate master dataset
        $perPage = $request->integer('per_page', 15);
        $logs = $query->orderBy('attendance_logs.checkin_at', 'desc')->paginate($perPage);

        // Map the collection to append human-readable calculations
        $logs->getCollection()->transform(function ($log) {
            $totalSec = (int)$log->session_duration;
            $h = intdiv($totalSec, 3600);
            $m = intdiv($totalSec % 3600, 60);
            
            $log->formatted_duration = "{$h}h {$m}m";
            $log->checkin_at = Carbon::parse($log->checkin_at);
            $log->checkout_at = $log->checkout_at ? Carbon::parse($log->checkout_at) : null;
            return $log;
        });

        return response()->json([
            'success' => true,
            'message' => 'Attendance daily unique summary compiled.',
            'excel_download_url' => $downloadUrl,
            'filters' => [
                'name' => $request->name ?? 'none',
                'start_date' => $request->start_date ?? 'today',
                'end_date' => $request->end_date ?? 'today',
                'search' => $request->search ?? 'none'
            ],
            'summary' => $summaryBlock,
            'data' => $logs->items(),
            'pagination' => [
                'total' => $logs->total(),
                'count' => $logs->count(),
                'per_page' => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'total_pages' => $logs->lastPage()
            ]
        ], 200);
    }

    /**
     * Fetch detailed diagnostic specs of a singular targeted attendance log block.
     * GET /api/v1/attendance/{id}
     */
 /**
     * Fetch detailed diagnostic specs of a singular targeted attendance log block.
     * GET /api/v1/attendance/{id}
     */
    public function show($id)
    {
        // 1. Locate the baseline requested log element
        $baseLog = AttendanceLog::find($id);

        if (!$baseLog) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance log item record matching this signature not found.'
            ], 404);
        }

        // 2. Query the exact day boundaries to assemble the enterprise style logic metrics 
        $dailyMetrics = AttendanceLog::with(['user:id,name,email,role_id'])
            ->where('user_id', $baseLog->user_id)
            ->whereRaw('DATE(checkin_at) = DATE(?)', [$baseLog->checkin_at])
            ->select(
                'user_id',
                DB::raw('DATE(checkin_at) as log_date'),
                'email',
                'device_type',
                'ip_address',
                'browser',
                'platform',
                'user_agent',
                
                // Absolute first punch of the day
                DB::raw("(SELECT MIN(inner_logs.checkin_at) FROM attendance_logs as inner_logs WHERE inner_logs.user_id = attendance_logs.user_id AND DATE(inner_logs.checkin_at) = DATE(attendance_logs.checkin_at)) as checkin_at"),
                
                // Current live status tracking the latest structural segment
                DB::raw("(
                    SELECT inner_logs.status 
                    FROM attendance_logs as inner_logs 
                    WHERE inner_logs.user_id = attendance_logs.user_id 
                    AND DATE(inner_logs.checkin_at) = DATE(attendance_logs.checkin_at)
                    ORDER BY inner_logs.checkin_at DESC LIMIT 1
                ) as status"),

                // Checkout Conditional Rule Pipeline
                DB::raw("(
                    SELECT CASE 
                        WHEN (SELECT inner_logs.status FROM attendance_logs as inner_logs WHERE inner_logs.user_id = attendance_logs.user_id AND DATE(inner_logs.checkin_at) = DATE(attendance_logs.checkin_at) ORDER BY inner_logs.checkin_at DESC LIMIT 1) = 'active' 
                        THEN NULL 
                        ELSE (SELECT MAX(inner_logs.checkout_at) FROM attendance_logs as inner_logs WHERE inner_logs.user_id = attendance_logs.user_id AND DATE(inner_logs.checkin_at) = DATE(attendance_logs.checkin_at))
                    END
                ) as checkout_at"),

                // Sum of cumulative completed durations
                DB::raw("(
                    SELECT SUM(inner_logs.session_duration) 
                    FROM attendance_logs as inner_logs 
                    WHERE inner_logs.user_id = attendance_logs.user_id 
                    AND DATE(inner_logs.checkin_at) = DATE(attendance_logs.checkin_at)
                ) as session_duration")
            )
            ->first();

        // 3. Format telemetry calculations into human-readable strings
        $totalSec = (int)$dailyMetrics->session_duration;
        $h = intdiv($totalSec, 3600);
        $m = intdiv($totalSec % 3600, 60);
        
        $dailyMetrics->id = $baseLog->id; // Preserve original fallback reference binding
        $dailyMetrics->formatted_duration = "{$h}h {$m}m";
        $dailyMetrics->checkin_at = Carbon::parse($dailyMetrics->checkin_at);
        $dailyMetrics->checkout_at = $dailyMetrics->checkout_at ? Carbon::parse($dailyMetrics->checkout_at) : null;

        return response()->json([
            'success' => true,
            'message' => 'Detailed structural log metrics resolved with matching timeline styles.',
            'data' => $dailyMetrics
        ], 200);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class AttendanceLogsController extends Controller
{
    /**
     * Isolated query builder helper to keep filtering identical 
     * across index analytics, pagination, and file exports.
     */
    private function buildAttendanceQuery(Request $request)
    {
        $query = AttendanceLog::with(['user:id,name,email,role_id']);

        // Filter by Specific Name
        if ($request->filled('name')) {
            $query->where('name', 'LIKE', "%{$request->name}%");
        }

        // Filter by Date Range
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $startDate = $request->filled('start_date')
                ? Carbon::parse($request->start_date)->startOfDay()
                : now()->subDays(30)->startOfDay();

            $endDate = $request->filled('end_date')
                ? Carbon::parse($request->end_date)->endOfDay()
                : now()->endOfDay();

            $query->whereBetween('checkin_at', [$startDate, $endDate]);
        }

        // Global Fuzzy Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('email', 'LIKE', "%{$search}%")
                    ->orWhere('checkin_address', 'LIKE', "%{$search}%")
                    ->orWhere('checkout_address', 'LIKE', "%{$search}%")
                    ->orWhere('device_type', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        return $query;
    }

    /**
     * Fetch a filtered, paginated list of attendance logs with summary metrics and a direct asset download link.
     * GET /api/v1/attendance
     */
    public function index(Request $request)
    {
        // 1. Build Base Query via helper
        $query = $this->buildAttendanceQuery($request);

        // 2. INDUSTRY STANDARD SUMMARY: Cloned state logic
        $summaryQuery = clone $query;
        $summaryMetrics = $summaryQuery->selectRaw("
            COUNT(*) as total_logs,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_sessions,
            SUM(CASE WHEN status != 'active' OR status IS NULL THEN 1 ELSE 0 END) as completed_sessions,
            IFNULL(SUM(session_duration), 0) as total_duration_seconds
        ")->first();

        // Calculate human-readable duration
        $totalSec = (int)$summaryMetrics->total_duration_seconds;
        $hours = intdiv($totalSec, 3600);
        $minutes = intdiv($totalSec % 3600, 60);

        $summaryBlock = [
            'total_logs' => (int)$summaryMetrics->total_logs,
            'active_sessions' => (int)$summaryMetrics->active_sessions,
            'completed_sessions' => (int)$summaryMetrics->completed_sessions,
            'total_hours_logged' => "{$hours}h {$minutes}m",
        ];

        // 3. GENERATE AND SAVE EXCEL FILE DIRECTLY TO DISK
        // Build unique cache key using hashed request parameters to ensure users with different filters get separate files
        $filterHash = md5(json_encode($request->only(['name', 'start_date', 'end_date', 'search'])));
        $fileName = "attendance_export_{$filterHash}.csv";

        // Ensure storage directory exists
        Storage::disk('public')->makeDirectory('exports');

        // Optional: Generate file dynamically only if it does not already exist on disk
        if (!Storage::disk('public')->exists("exports/{$fileName}")) {

            // Fetch the exact filtered collection match
            $logs = $this->buildAttendanceQuery($request)->orderBy('checkin_at', 'desc')->get();

            // Create a memory stream pointer
            $tempFile = fopen('php://temp', 'r+');

            // Inject structural UTF-8 BOM so Excel decodes characters properly
            fprintf($tempFile, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Set Spreadsheet Headings Row
            fputcsv($tempFile, [
                'Log ID',
                'Employee Name',
                'Email Address',
                'Status',
                'Check-In Time',
                'Check-Out Time',
                'Duration',
                'IP Address',
                'Device Type'
            ]);

            // Stream Rows
            foreach ($logs as $log) {
                fputcsv($tempFile, [
                    $log->id,
                    $log->name ?? ($log->user?->name ?? 'N/A'),
                    $log->email,
                    ucfirst($log->status),
                    $log->checkin_at ? $log->checkin_at->toDateTimeString() : '—',
                    $log->checkout_at ? $log->checkout_at->toDateTimeString() : '—',
                    $log->formatted_duration_attribute,
                    $log->ip_address,
                    $log->device_type
                ]);
            }

            // Rewind stream pointer and write structural context to public disk storage
            rewind($tempFile);
            Storage::disk('public')->put("exports/{$fileName}", stream_get_contents($tempFile));
            fclose($tempFile);
        }

        // Get the absolute URL link pointing directly to the public asset file
        $downloadUrl = asset("storage/exports/{$fileName}");

        // 4. Order and Paginate Results
        $perPage = $request->integer('per_page', 15);
        $logs = $query->orderBy('checkin_at', 'desc')->paginate($perPage);

        // 5. Map the collection to append custom virtual fields
        $logs->getCollection()->transform(function ($log) {
            $log->formatted_duration = $log->formatted_duration_attribute;
            return $log;
        });

        return response()->json([
            'success' => true,
            'message' => 'Attendance history and aggregate summary compiled.',
            'excel_download_url' => $downloadUrl, // <-- Permanent static file asset URL string
            'filters' => [
                'name' => $request->name ?? 'none',
                'start_date' => $request->start_date ?? 'none',
                'end_date' => $request->end_date ?? 'none',
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
    public function show($id)
    {
        $log = AttendanceLog::with(['user'])->find($id);

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance log item record matching this signature not found.'
            ], 404);
        }

        $log->formatted_duration = $log->formatted_duration_attribute;

        return response()->json([
            'success' => true,
            'message' => 'Detailed structural log metrics resolved.',
            'data' => $log
        ], 200);
    }
}

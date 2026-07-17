<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use Carbon\carbon;

class UserAttendanceController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // Office coordinates — adjust as needed or move to config/services.php
    // ─────────────────────────────────────────────────────────────────────────
    private const OFFICE_LAT    = 20.282603;
    private const OFFICE_LNG    = 85.857688;
    private const OFFICE_RADIUS = 500; // metres


    public function checkin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address'   => 'nullable|string|max:500',
            'accuracy'  => 'nullable|numeric|min:0',
        ]);

        $distance      = $this->calculateDistance(
            $validated['latitude'],
            $validated['longitude'],
            self::OFFICE_LAT,
            self::OFFICE_LNG
        );
        $checkinStatus = $distance <= self::OFFICE_RADIUS ? 'in-office' : 'out-office';

        $user  = $request->user();           // Sanctum resolves from Bearer token
        $agent = $this->makeAgent($request);
        $ip    = $this->resolveIp($request);

        $log = AttendanceLog::create([
            'user_id'          => $user->id,
            'name'             => $user->name,
            'email'            => $user->email,
            'checkin_latitude' => $validated['latitude'],
            'checkin_longitude'=> $validated['longitude'],
            'checkin_address'  => $validated['address']  ?? null,
            'checkin_accuracy' => $validated['accuracy'] ?? null,
            'checkin_at'       => now(),
            'status'           => 'active',
            'checkin_status'   => $checkinStatus,
            'ip_address'       => $ip,
            'device_type'      => $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop'),
            'browser'          => $agent->browser()  ?: null,
            'platform'         => $agent->platform() ?: null,
            'user_agent'       => $request->userAgent(),
        ]);

        return $this->success('Checked in ' . ($checkinStatus === 'in-office' ? 'inside' : 'outside') . ' office.', [
            'log_id'         => $log->id,
            'checkin_at'     => $log->checkin_at->toIso8601String(),
            'checkin_status' => $checkinStatus,
            'distance'       => round($distance) . ' meters',
            'location'       => [
                'latitude'  => $log->checkin_latitude,
                'longitude' => $log->checkin_longitude,
                'address'   => $log->checkin_address,
            ],
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/attendance/checkout
    // Headers: Authorization: Bearer {token}  |  Accept: application/json
    // Body (JSON): { latitude, longitude, address?, accuracy? }
    // ─────────────────────────────────────────────────────────────────────────
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address'   => 'nullable|string|max:500',
            'accuracy'  => 'nullable|numeric|min:0',
        ]);

        $distance       = $this->calculateDistance(
            $validated['latitude'],
            $validated['longitude'],
            self::OFFICE_LAT,
            self::OFFICE_LNG
        );
        $checkoutStatus = $distance <= self::OFFICE_RADIUS ? 'in-office' : 'out-office';

        $user = $request->user();

        $log = AttendanceLog::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('checkin_at')
            ->first();

        if (! $log) {
            return $this->error('No active check-in session found. Please check in first.', 422);
        }

        $checkoutAt      = now();
        $sessionDuration = (int) $checkoutAt->diffInSeconds($log->checkin_at);

        $log->update([
            'checkout_latitude'  => $validated['latitude'],
            'checkout_longitude' => $validated['longitude'],
            'checkout_address'   => $validated['address']  ?? null,
            'checkout_accuracy'  => $validated['accuracy'] ?? null,
            'checkout_at'        => $checkoutAt,
            'session_duration'   => $sessionDuration,
            'checkout_status'    => $checkoutStatus,
            'status'             => 'completed',
        ]);

        return $this->success('Checked out ' . ($checkoutStatus === 'in-office' ? 'inside' : 'outside') . ' office.', [
            'log_id'          => $log->id,
            'checkin_at'      => $log->checkin_at->toIso8601String(),
            'checkout_at'     => $log->checkout_at->toIso8601String(),
            'session_duration'=> $log->formatted_duration,   // HH:MM:SS
            'checkout_status' => $checkoutStatus,
            'distance'        => round($distance) . ' meters',
            'location'        => [
                'latitude'  => $log->checkout_latitude,
                'longitude' => $log->checkout_longitude,
                'address'   => $log->checkout_address,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/attendance/status
    // Returns the current active session (if any) for the authenticated user.
    // ─────────────────────────────────────────────────────────────────────────
   // ─────────────────────────────────────────────────────────────────────────
    // GET /api/attendance/status
    // Returns the current active session or custom calendar state (Sunday/Holiday)
    // ─────────────────────────────────────────────────────────────────────────
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = now()->startOfDay();

        // 1. Evaluate for active checked-in sessions first
        $activeLog = AttendanceLog::where('user_id', $user->id)
            ->active()
            ->latest('checkin_at')
            ->first();

        // 2. Compute calendar tracking infrastructure state context
        $holidayName = null;
        $isSunday = now()->dayOfWeek === Carbon::SUNDAY;

        // Query active system holidays falling within today's date boundaries
        $activeHoliday = \App\Models\Holiday::where('status', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        if ($activeHoliday) {
            $holidayName = $activeHoliday->name;
        }

        return $this->success('Status fetched successfully.', [
            'is_checked_in'   => (bool) $activeLog,
            'is_sunday'       => $isSunday,
            'is_holiday'      => (bool) $holidayName,
            'holiday_name'    => $holidayName,
            'calendar_label'  => $holidayName ?: ($isSunday ? 'Sunday' : 'Standard Working Day'),
            'active_session'  => $activeLog ? [
                'log_id'          => $activeLog->id,
                'checkin_at'      => $activeLog->checkin_at->toIso8601String(),
                'checkin_status'  => $activeLog->checkin_status,
                'checkin_address' => $activeLog->checkin_address,
                'location'        => [
                    'latitude'  => $activeLog->checkin_latitude,
                    'longitude' => $activeLog->checkin_longitude,
                ],
            ] : null,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/attendance/history?per_page=15&page=1&date=2026-06-19
    // Returns paginated attendance history for the authenticated user.
    // ─────────────────────────────────────────────────────────────────────────
    public function history(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'per_page'   => 'nullable|integer|min:1|max:100',
            'date'       => 'nullable|date_format:Y-m-d',
            'start_date' => 'nullable|date_format:Y-m-d|required_with:end_date',
            'end_date'   => 'nullable|date_format:Y-m-d|required_with:start_date|after_or_equal:start_date',
            'status'     => 'nullable|in:active,completed',
        ]);

        $query = AttendanceLog::where('user_id', $request->user()->id)
            ->latest('checkin_at');

        // Single-day shortcut — takes priority over start/end range
        if ($request->filled('date')) {
            $query->whereDate('checkin_at', $request->date);
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            // Include the full end day (up to 23:59:59)
            $query->whereDate('checkin_at', '>=', $request->start_date)
                  ->whereDate('checkin_at', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->integer('per_page', 15);
        $logs    = $query->paginate($perPage);

        // Summary stats for the filtered range
        $totalDurationSeconds = AttendanceLog::where('user_id', $request->user()->id)
            ->where('status', 'completed')
            ->when($request->filled('date'), fn ($q) => $q->whereDate('checkin_at', $request->date))
            ->when(
                ! $request->filled('date') && $request->filled('start_date'),
                fn ($q) => $q->whereDate('checkin_at', '>=', $request->start_date)
                             ->whereDate('checkin_at', '<=', $request->end_date)
            )
            ->sum('session_duration');

        $hours   = intdiv((int) $totalDurationSeconds, 3600);
        $minutes = intdiv((int) $totalDurationSeconds % 3600, 60);

        $logs->getCollection()->transform(fn ($log) => [
            'log_id'           => $log->id,
            'status'           => $log->status,
            'checkin_at'       => $log->checkin_at?->toIso8601String(),
            'checkout_at'      => $log->checkout_at?->toIso8601String(),
            'session_duration' => $log->formatted_duration,
            'checkin_status'   => $log->checkin_status,
            'checkout_status'  => $log->checkout_status,
            'checkin_location' => [
                'latitude'  => $log->checkin_latitude,
                'longitude' => $log->checkin_longitude,
                'address'   => $log->checkin_address,
            ],
            'checkout_location'=> $log->checkout_at ? [
                'latitude'  => $log->checkout_latitude,
                'longitude' => $log->checkout_longitude,
                'address'   => $log->checkout_address,
            ] : null,
            'device'           => [
                'type'     => $log->device_type,
                'browser'  => $log->browser,
                'platform' => $log->platform,
            ],
            'ip_address'       => $log->ip_address,
        ]);

        // Build applied filter info for the response
        $dateRange = null;
        if ($request->filled("date")) {
            $dateRange = ["from" => $request->date, "to" => $request->date];
        } elseif ($request->filled("start_date")) {
            $dateRange = ["from" => $request->start_date, "to" => $request->end_date];
        }

        return $this->success("History fetched successfully.", [
            "filters"    => [
                "date_range"  => $dateRange,
                "status"      => $request->filled("status") ? $request->status : "all",
            ],
            "summary"    => [
                "total_records"    => $logs->total(),
                "total_time"       => sprintf("%02d:%02d", $hours, $minutes),
                "total_time_label" => $hours . "h " . $minutes . "m",
            ],
            "records"    => $logs->items(),
            "pagination" => [
                "current_page" => $logs->currentPage(),
                "per_page"     => $logs->perPage(),
                "total"        => $logs->total(),
                "last_page"    => $logs->lastPage(),
                "has_more"     => $logs->hasMorePages(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/attendance/today
    // Returns all of today's sessions for the authenticated user.
    // ─────────────────────────────────────────────────────────────────────────
    public function today(Request $request): JsonResponse
    {
        $user = $request->user();

        $logs = AttendanceLog::where('user_id', $user->id)
            ->whereDate('checkin_at', today())
            ->latest('checkin_at')
            ->get()
            ->map(fn ($log) => [
                'log_id'           => $log->id,
                'status'           => $log->status,
                'checkin_at'       => $log->checkin_at?->toIso8601String(),
                'checkout_at'      => $log->checkout_at?->toIso8601String(),
                'session_duration' => $log->formatted_duration,
                'checkin_status'   => $log->checkin_status,
                'checkout_status'  => $log->checkout_status,
                'checkin_address'  => $log->checkin_address,
                'checkout_address' => $log->checkout_address,
            ]);

        $totalSeconds = AttendanceLog::where('user_id', $user->id)
            ->whereDate('checkin_at', today())
            ->where('status', 'completed')
            ->sum('session_duration');

        $hours   = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);

        return $this->success('Today\'s attendance fetched.', [
            'date'          => today()->toDateString(),
            'sessions'      => $logs,
            'total_sessions'=> $logs->count(),
            'total_time'    => sprintf('%02d:%02d', $hours, $minutes), // HH:MM
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Haversine formula — returns distance in metres
    // ─────────────────────────────────────────────────────────────────────────
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R    = 6371000; // Earth radius in metres
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) ** 2
           + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;

        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────
    private function makeAgent(Request $request): Agent
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());
        return $agent;
    }

    private function resolveIp(Request $request): string
    {
        $forwarded = $request->header('X-Forwarded-For');
        return $forwarded ? trim(explode(',', $forwarded)[0]) : $request->ip();
    }

    private function success(string $message, array $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success'   => true,
            'message'   => $message,
            'data'      => $data,
            'timestamp' => now()->toIso8601String(),
        ], $status);
    }

    private function error(string $message, int $status = 400, array $errors = []): JsonResponse
    {
        return response()->json([
            'success'   => false,
            'message'   => $message,
            'errors'    => $errors ?: null,
            'timestamp' => now()->toIso8601String(),
        ], $status);
    }
}

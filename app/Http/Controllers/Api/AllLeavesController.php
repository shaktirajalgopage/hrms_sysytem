<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AllLeavesController extends Controller
{
    /**
     * Fetch a filtered, paginated list of leaves accompanied by an enterprise metrics summary.
     * GET /api/v1/leaves
     */
    public function index(Request $request)
    {
        // 1. Base Query with relationships eager loaded to prevent N+1 queries
        $query = Leave::with([
            'employee.user:id,name,email',
            'currentApprover.user:id,name,email',
            'histories'
        ]);

        // 2. FILTER: By Status Integer Structure
        // (0=rejected, 1=pending, 2=approved_by_team_lead, 3=approved_by_manager, 4=approved)
        if ($request->has('status') && $request->input('status') !== null && $request->input('status') !== '') {
            $query->where('status', $request->integer('status'));
        }

        // 3. FILTER: By Leave Type (e.g., casual, sick, annual)
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        // 4. FILTER: By Employee ID
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // 5. FILTER: Date Range (Checks if leave overlaps with requested range)
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $startDate = $request->filled('start_date')
                ? Carbon::parse($request->start_date)->startOfDay()
                : now()->startOfYear();

            $endDate = $request->filled('end_date')
                ? Carbon::parse($request->end_date)->endOfDay()
                : now()->endOfYear();

            $query->where(function ($dateQuery) use ($startDate, $endDate) {
                $dateQuery->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            });
        }

        // 6. FILTER: Global Fuzzy Search (Title, Reason, Type, or Employee Name/Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('leave_reason', 'LIKE', "%{$search}%")
                    ->orWhere('leave_type', 'LIKE', "%{$search}%")
                    ->orWhereHas('employee.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        // 7. INDUSTRY STANDARD SUMMARY: Calculate aggregate metrics across the entire FILTERED dataset
        $metricsQuery = clone $query;
        $todayStart = Carbon::today()->toDateTimeString();

        $aggregates = $metricsQuery->selectRaw("
            COUNT(*) as total_requests,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as initial_pending_count,
            SUM(CASE WHEN status IN (2, 3) THEN 1 ELSE 0 END) as pipeline_pending_count,
            SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as approved_count,
            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as rejected_count,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as applied_today_count
        ", [$todayStart])->first();

        $summaryBlock = [
            'total_applications' => (int)$aggregates->total_requests,
            'today_leave_applied' => (int)$aggregates->applied_today_count,
            'total_pending'      => (int)($aggregates->initial_pending_count + $aggregates->pipeline_pending_count),
            'pending_initial'    => (int)$aggregates->initial_pending_count,   // Status 1
            'pending_pipeline'   => (int)$aggregates->pipeline_pending_count,  // Status 2 or 3 (TL/Manager approved, awaiting finalization)
            'fully_approved'     => (int)$aggregates->approved_count,          // Status 4
            'rejected'           => (int)$aggregates->rejected_count,          // Status 0
        ];

        // 8. Order and Paginate Results
        $perPage = $request->integer('per_page', 15);
        $leaves = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Leave records architecture retrieved successfully.',
            'filters_applied' => [
                'status'      => $request->has('status') && $request->input('status') !== '' ? $request->status : 'all',
                'leave_type'  => $request->leave_type ?? 'all',
                'employee_id' => $request->employee_id ?? 'all',
                'start_date'  => $request->start_date ?? 'none',
                'end_date'    => $request->end_date ?? 'none',
                'search'      => $request->search ?? 'none'
            ],
            'summary'    => $summaryBlock,
            'data'       => $leaves->items(),
            'pagination' => [
                'total'        => $leaves->total(),
                'count'        => $leaves->count(),
                'per_page'     => $leaves->perPage(),
                'current_page' => $leaves->currentPage(),
                'total_pages'  => $leaves->lastPage()
            ]
        ], 200);
    }

    /**
     * Fetch complete details of a specific leave application with comprehensive history tracking.
     * GET /api/v1/leaves/{id}
     */
    public function show($id)
    {
        $leave = Leave::with([
            'employee.user',
            'currentApprover.user',
            'histories.approver.user'
        ])->find($id);

        if (!$leave) {
            return response()->json([
                'success' => false,
                'message' => 'Requested leave entry not found in system storage.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Leave log detailed telemetry resolved successfully.',
            'data'    => $leave
        ], 200);
    }
}
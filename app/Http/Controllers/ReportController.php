<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $projectStats = [
            'total' => Project::count(),
            'active' => Project::where('status', 'active')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'on_hold' => Project::where('status', 'on_hold')->count(),
            'avg_progress' => (int) round(Project::avg('progress') ?? 0),
        ];

        $taskStats = [
            'total' => Task::count(),
            'todo' => Task::where('status', 'todo')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'review' => Task::where('status', 'review')->count(),
            'completed' => Task::where('status', 'completed')->count(),
            'overdue' => Task::where('status', '!=', 'completed')
                ->whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->count(),
        ];

        $ticketStats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
            'critical_open' => Ticket::whereIn('status', ['open', 'in_progress'])
                ->where('priority', 'critical')->count(),
        ];

        // Project-wise progress table
        $projectProgress = Project::withCount([
            'tasks',
            'tasks as completed_tasks_count' => fn ($q) => $q->where('status', 'completed'),
        ])->orderByDesc('progress')->take(10)->get();

        // Employee-wise workload / engagement (tasks currently assigned + completed)
        $employeeWorkload = User::withCount([
            'assignedTasks as open_tasks_count' => fn ($q) => $q->where('status', '!=', 'completed'),
            'assignedTasks as completed_tasks_count' => fn ($q) => $q->where('status', 'completed'),
        ])->having('open_tasks_count', '>', 0)
            ->orHaving('completed_tasks_count', '>', 0)
            ->orderByDesc('open_tasks_count')
            ->take(10)
            ->get();

        // Ticket trend for the last 6 months
        $ticketTrend = Ticket::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        return view('reports.index', compact(
            'projectStats', 'taskStats', 'ticketStats',
            'projectProgress', 'employeeWorkload', 'ticketTrend'
        ));
    }
}

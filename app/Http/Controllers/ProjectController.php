<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::withCount('tasks')->with('department');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%")
                    ->orWhere('client_name', 'like', "%{$request->search}%");
            });
        }

        $projects = $query->latest()->paginate(9)->withQueryString();

        $stats = [
            'total' => Project::count(),
            'active' => Project::where('status', 'active')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'on_hold' => Project::where('status', 'on_hold')->count(),
        ];

        return view('   projects.index', compact('projects', 'stats'));
    }

    public function create()
    {
        $departments = Department::where('status', 1)->get();
        $employees = User::where('status', 1)->get();

        return view('projects.create', compact('departments', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_name' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'budget' => 'nullable|numeric',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $validated['code'] = 'PRJ-'.str_pad((string) (Project::max('id') + 1), 4, '0', STR_PAD_LEFT);
        $validated['created_by'] = Auth::id();

        $project = DB::transaction(function () use ($validated, $request) {
            $project = Project::create($validated);

            $memberIds = $request->input('members', []);
            if (! empty($memberIds)) {
                $project->members()->attach($memberIds, ['role' => 'member']);
            }
            $project->members()->syncWithoutDetaching([Auth::id() => ['role' => 'manager']]);

            return $project;
        });

        return redirect()->route('projects.show', $project->id)
            ->with('success', __('Project created successfully.'));
    }

    public function show(Project $project)
    {
        $project->load([
            'department', 'members', 'creator',
            'tasks' => fn ($q) => $q->orderBy('sort_order'),
            'tasks.assignee',
            'tasks.comments.user',
            'tickets' => fn ($q) => $q->latest()->limit(5),
        ]);

        $tasksByStatus = $project->tasks->groupBy('status');
        $employees = User::where('status', 1)->get();

        return view('projects.show', compact('project', 'tasksByStatus', 'employees'));
    }

    public function edit(Project $project)
    {
        $departments = Department::where('status', 1)->get();
        $employees = User::where('status', 1)->get();
        $project->load('members');

        return view('projects.edit', compact('project', 'departments', 'employees'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_name' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'budget' => 'nullable|numeric',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $project->update($validated);
        $project->members()->sync($request->input('members', []));

        return redirect()->route('projects.show', $project->id)
            ->with('success', __('Project updated successfully.'));
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', __('Project deleted successfully.'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|numeric',
        ]);

        $validated['project_id'] = $project->id;
        $validated['created_by'] = Auth::id();
        $validated['sort_order'] = $project->tasks()->max('sort_order') + 1;

        $task = Task::create($validated);

        $project->recalculateProgress();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'task' => $task->load('assignee')]);
        }

        return back()->with('success', __('Task added successfully.'));
    }

    /** Drag-and-drop kanban update: change column (status) and optionally reorder. */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,review,completed',
            'sort_order' => 'nullable|integer',
        ]);

        $task->applyStatusChange($validated['status'], null, Auth::id(), __('Moved on Kanban board'));

        if (array_key_exists('sort_order', $validated)) {
            $task->update(['sort_order' => $validated['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'task' => $task->fresh(),
            'project_progress' => $task->project->fresh()->progress,
        ]);
    }

    public function updateProgress(Request $request, Task $task)
    {
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $status = $validated['progress'] >= 100 ? 'completed' : $task->status;

        $task->applyStatusChange($status, $validated['progress'], Auth::id(), __('Progress updated'));

        return response()->json([
            'success' => true,
            'task' => $task->fresh(),
            'project_progress' => $task->project->fresh()->progress,
        ]);
    }

    public function storeComment(Request $request, Task $task)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = $task->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'comment' => $comment->load('user')]);
        }

        return back()->with('success', __('Comment added.'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric',
        ]);

        $task->update($validated);

        return back()->with('success', __('Task updated successfully.'));
    }

    public function destroy(Task $task)
    {
        $project = $task->project;
        $task->delete();
        $project->recalculateProgress();

        return back()->with('success', __('Task removed.'));
    }
}

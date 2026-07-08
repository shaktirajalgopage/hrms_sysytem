<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['project', 'task', 'raisedBy', 'assignee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_number', 'like', "%{$request->search}%")
                    ->orWhere('subject', 'like', "%{$request->search}%");
            });
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];

        $projects = Project::orderBy('title')->get();
        $employees = User::where('status', 1)->get();

        return view('tickets.index', compact('tickets', 'stats', 'projects', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'task_id' => 'nullable|exists:tasks,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:bug,support,feature_request,other',
            'priority' => 'required|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['raised_by'] = Auth::id();

        $ticket = Ticket::create($validated);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', __('Ticket :number generated successfully.', ['number' => $ticket->ticket_number]));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['project', 'task', 'raisedBy', 'assignee', 'replies.user']);
        $employees = User::where('status', 1)->get();

        return view('tickets.show', compact('ticket', 'employees'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal' => 'nullable|boolean',
        ]);

        $ticket->replies()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'is_internal' => $request->boolean('is_internal'),
        ]);

        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', __('Reply added.'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket->update([
            'status' => $validated['status'],
            'resolved_at' => in_array($validated['status'], ['resolved', 'closed']) ? now() : null,
        ]);

        return back()->with('success', __('Ticket status updated.'));
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $ticket->update($validated);

        return back()->with('success', __('Ticket assigned.'));
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', __('Ticket deleted.'));
    }
}

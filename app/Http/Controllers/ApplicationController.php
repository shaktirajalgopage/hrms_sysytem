<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationActivity;
use App\Models\Candidate;
use App\Models\InterviewRound;
use App\Models\JobPosition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{

private function routePrefix()
{
    return auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
}
    // ─── Index (List View) ────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Application::with(['candidate', 'jobPosition', 'assignedTo', 'interviewRounds'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('candidate', fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%"));
        }

        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }

        if ($request->filled('position')) {
            $query->where('job_position_id', $request->position);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->paginate(15)->withQueryString();

        $stats = [
            'total'       => Application::count(),
            'active'      => Application::where('status', 'active')->count(),
            'hired'       => Application::where('stage', 'hired')->count(),
            'this_week'   => Application::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return view('admin.interviews.index', [
            'applications' => $applications,
            'stats'        => $stats,
            'stages'       => Application::stages(),
            'positions'    => JobPosition::where('is_active', true)->pluck('title', 'id'),
        ]);
    }

    // ─── Kanban View ──────────────────────────────────────────────────────────

    public function kanban(Request $request)
    {
        $stages = Application::stages();
        $kanbanStages = array_filter($stages, fn($s) => $s['order'] <= 6); // exclude rejected from main board

        $columns = [];
        foreach ($kanbanStages as $key => $config) {
            $columns[$key] = [
                'config'       => $config,
                'applications' => Application::with(['candidate', 'jobPosition', 'interviewRounds'])
                    ->where('stage', $key)
                    ->when($request->filled('position'), fn($q) => $q->where('job_position_id', $request->position))
                    ->latest()
                    ->get(),
            ];
        }

        return view('admin.interviews.kanban', [
            'columns'   => $columns,
            'positions' => JobPosition::where('is_active', true)->pluck('title', 'id'),
        ]);
    }

    // ─── Create / Store ───────────────────────────────────────────────────────

    public function create()
    {
        return view('admin.interviews.create', [
            'positions'  => JobPosition::where('is_active', true)->get(),
            'users'      => User::orderBy('name')->pluck('name', 'id'),
            'sources'    => Application::sources(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'candidate_name'    => 'required|string|max:255',
            'candidate_email'   => 'required|email|max:255',
            'candidate_phone'   => 'nullable|string|max:20',
            'linkedin_url'      => 'nullable|url',
            'portfolio_url'     => 'nullable|url',
            'cv'                => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'job_position_id'   => 'required|exists:job_positions,id',
            'source'            => 'nullable|string',
            'expected_salary'   => 'nullable|numeric',
            'available_from'    => 'nullable|date',
            'assigned_to'       => 'nullable|exists:users,id',
            'notes'             => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            // Upsert candidate
            $candidate = Candidate::firstOrCreate(
                ['email' => $request->candidate_email],
                [
                    'name'          => $request->candidate_name,
                    'phone'         => $request->candidate_phone,
                    'linkedin_url'  => $request->linkedin_url,
                    'portfolio_url' => $request->portfolio_url,
                    'notes'         => $request->notes,
                ]
            );

            // Upload CV
            if ($request->hasFile('cv')) {
                $file = $request->file('cv');
                $path = $file->store('cvs', 'private');
                $candidate->update([
                    'cv_path'          => $path,
                    'cv_original_name' => $file->getClientOriginalName(),
                ]);
            }

            // Create application
            $application = Application::create([
                'candidate_id'    => $candidate->id,
                'job_position_id' => $request->job_position_id,
                'stage'           => 'applied',
                'status'          => 'active',
                'source'          => $request->source,
                'expected_salary' => $request->expected_salary,
                'available_from'  => $request->available_from,
                'assigned_to'     => $request->assigned_to,
                'created_by'      => Auth::id(),
            ]);

            // Log activity
            $this->logActivity($application, 'cv_uploaded', 'Application created and CV uploaded');
        });

        return redirect()->route("{$this->routePrefix()}applications.index")
            ->with('success', 'Application submitted successfully.');
    }

    // ─── Show (Detail) ────────────────────────────────────────────────────────

    public function show(Application $application)
    {
        $application->load([
            'candidate', 'jobPosition', 'assignedTo',
            'interviewRounds.interviewer',
            'activities.creator',
        ]);

        return view('admin.interviews.show', [
            'application'   => $application,
            'stages'        => Application::stages(),
            'users'         => User::orderBy('name')->pluck('name', 'id'),
            'roundOutcomes' => InterviewRound::outcomes(),
            'roundModes'    => InterviewRound::modes(),
        ]);
    }

    // ─── Edit / Update ────────────────────────────────────────────────────────

    public function edit(Application $application)
    {
        $application->load('candidate');

        return view('admin.interviews.edit', [
            'application' => $application,
            'positions'   => JobPosition::where('is_active', true)->get(),
            'users'       => User::orderBy('name')->pluck('name', 'id'),
            'sources'     => Application::sources(),
        ]);
    }

    public function update(Request $request, Application $application)
    {
        $request->validate([
            'candidate_name'  => 'required|string|max:255',
            'candidate_phone' => 'nullable|string|max:20',
            'linkedin_url'    => 'nullable|url',
            'portfolio_url'   => 'nullable|url',
            'cv'              => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'job_position_id' => 'required|exists:job_positions,id',
            'source'          => 'nullable|string',
            'expected_salary' => 'nullable|numeric',
            'available_from'  => 'nullable|date',
            'assigned_to'     => 'nullable|exists:users,id',
            'status'          => 'required|in:active,on_hold,rejected,withdrawn',
        ]);

        DB::transaction(function () use ($request, $application) {
            $application->candidate->update([
                'name'          => $request->candidate_name,
                'phone'         => $request->candidate_phone,
                'linkedin_url'  => $request->linkedin_url,
                'portfolio_url' => $request->portfolio_url,
            ]);

            if ($request->hasFile('cv')) {
                if ($application->candidate->cv_path) {
                    Storage::disk('private')->delete($application->candidate->cv_path);
                }
                $file = $request->file('cv');
                $application->candidate->update([
                    'cv_path'          => $file->store('cvs', 'private'),
                    'cv_original_name' => $file->getClientOriginalName(),
                ]);
                $this->logActivity($application, 'cv_uploaded', 'CV updated');
            }

            $application->update([
                'job_position_id' => $request->job_position_id,
                'source'          => $request->source,
                'expected_salary' => $request->expected_salary,
                'available_from'  => $request->available_from,
                'assigned_to'     => $request->assigned_to,
                'status'          => $request->status,
            ]);
        });

        return redirect()->route("{$this->routePrefix()}applications.show", $application)
            ->with('success', 'Application updated successfully.');
    }

    // ─── Advance Stage ────────────────────────────────────────────────────────

    public function advanceStage(Request $request, Application $application)
    {
        $request->validate([
            'stage' => 'required|in:' . implode(',', array_keys(Application::stages())),
        ]);

        $oldStage = $application->stage_label;
        $application->update(['stage' => $request->stage]);
        $newStage = $application->stage_label;

        $this->logActivity(
            $application,
            'stage_changed',
            "Stage moved from <strong>{$oldStage}</strong> to <strong>{$newStage}</strong>"
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'stage' => $request->stage]);
        }

        return back()->with('success', "Stage updated to {$newStage}.");
    }

    // ─── Schedule Interview Round ─────────────────────────────────────────────

    public function scheduleRound(Request $request, Application $application)
    {
        $request->validate([
            'round_name'       => 'required|string|max:255',
            'round_type'       => 'required|in:screening,technical,hr,final,other',
            'mode'             => 'required|in:online,in_person,phone',
            'scheduled_at'     => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'interviewer_id'   => 'nullable|exists:users,id',
            'meeting_link'     => 'nullable|url',
            'location'         => 'nullable|string|max:255',
        ]);

        $round = $application->interviewRounds()->create([
            ...$request->only([
                'round_name', 'round_type', 'mode', 'scheduled_at',
                'duration_minutes', 'interviewer_id', 'meeting_link', 'location',
            ]),
            'outcome'    => 'pending',
            'created_by' => Auth::id(),
        ]);

        $this->logActivity(
            $application,
            'interview_scheduled',
            "Interview scheduled: <strong>{$round->round_name}</strong> on " . $round->scheduled_at->format('M d, Y H:i')
        );

        return back()->with('success', 'Interview round scheduled.');
    }

    // ─── Update Round Outcome ─────────────────────────────────────────────────

    public function updateRound(Request $request, Application $application, InterviewRound $round)
    {
        $request->validate([
            'outcome'        => 'required|in:pending,passed,failed,no_show,rescheduled',
            'rating'         => 'nullable|integer|min:1|max:5',
            'feedback'       => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $round->update($request->only(['outcome', 'rating', 'feedback', 'internal_notes']));

        $this->logActivity(
            $application,
            'interview_completed',
            "Interview <strong>{$round->round_name}</strong> marked as <strong>{$round->outcome_label}</strong>"
        );

        return back()->with('success', 'Interview round updated.');
    }

    // ─── CV Download ──────────────────────────────────────────────────────────

    public function downloadCv(Application $application)
    {
        $candidate = $application->candidate;

        if (!$candidate->cv_path || !Storage::disk('private')->exists($candidate->cv_path)) {
            return back()->with('error', 'CV not found.');
        }

        return Storage::disk('private')->download(
            $candidate->cv_path,
            $candidate->cv_original_name ?? 'cv.pdf'
        );
    }

    // ─── Kanban Stage Update (AJAX) ───────────────────────────────────────────

    public function updateStageAjax(Request $request, Application $application)
    {
        $request->validate([
            'stage' => 'required|in:' . implode(',', array_keys(Application::stages())),
        ]);

        $application->update(['stage' => $request->stage]);

        $this->logActivity(
            $application,
            'stage_changed',
            "Stage updated to <strong>{$application->stage_label}</strong> via Kanban board"
        );

        return response()->json(['success' => true]);
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(Application $application)
    {
        $application->delete();

        return redirect()->route('applications.index')
            ->with('success', 'Application deleted.');
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function logActivity(Application $application, string $type, string $description, array $meta = []): void
    {
        ApplicationActivity::create([
            'application_id' => $application->id,
            'type'           => $type,
            'description'    => $description,
            'meta'           => $meta ?: null,
            'created_by'     => Auth::id(),
        ]);
    }
}

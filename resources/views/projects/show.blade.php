@extends('layouts.admin')

@section('title')
    {{ $project->title }}
@endsection

@section('header')
    <h1 class="h3 mb-3"><strong>{{ $project->title }}</strong></h1>
@endsection

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $statusColor = $project->statusColorMap[$project->status] ?? 'secondary';
        $priorityColor = $project->priorityColorMap[$project->priority] ?? 'secondary';
        $stats = $project->task_stats;
    @endphp

    {{-- Header summary panel --}}
    <section class="row g-3">
        <div class="col-lg-8">
            <div class="card panel-card border-0 h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                        <div>
                            <span class="project-code">{{ $project->code }}</span>
                            <h4 class="fw-bold mt-1 mb-1">{{ $project->title }}</h4>
                            <p class="text-muted small mb-2">{{ $project->description ?: __('No description provided.') }}</p>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-soft-{{ $statusColor }} text-{{ $statusColor }} rounded-pill px-2">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span>
                                <span class="badge bg-soft-{{ $priorityColor }} text-{{ $priorityColor }} rounded-pill px-2">{{ ucfirst($project->priority) }} {{ __('priority') }}</span>
                                @if($project->client_name)
                                    <span class="badge bg-light text-secondary border rounded-pill px-2"><i class="fa-solid fa-building me-1"></i>{{ $project->client_name }}</span>
                                @endif
                                @if($project->isOverdue())
                                    <span class="badge bg-soft-danger text-danger rounded-pill px-2"><i class="fa-solid fa-triangle-exclamation me-1"></i>{{ __('Overdue') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-light btn-sm rounded-pill px-3"><i class="fa-solid fa-pen me-1"></i>{{ __('Edit') }}</a>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fs-xs text-muted fw-bold text-uppercase">{{ __('Overall Progress') }}</span>
                            <span class="fs-xs fw-bold text-dark">{{ $project->progress }}%</span>
                        </div>
                        <div class="ptm-progress">
                            <div class="ptm-progress-bar {{ $project->progress >= 100 ? 'is-complete' : '' }}" style="width: {{ $project->progress }}%"></div>
                        </div>
                    </div>

                    <div class="row g-2 mt-3 text-center">
                        <div class="col">
                            <div class="fw-bold fs-5 text-dark">{{ $stats['total'] }}</div>
                            <div class="fs-3xs text-muted text-uppercase fw-bold">{{ __('Total') }}</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold fs-5 text-secondary">{{ $stats['todo'] }}</div>
                            <div class="fs-3xs text-muted text-uppercase fw-bold">{{ __('To Do') }}</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold fs-5 text-primary">{{ $stats['in_progress'] }}</div>
                            <div class="fs-3xs text-muted text-uppercase fw-bold">{{ __('In Progress') }}</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold fs-5 text-warning">{{ $stats['review'] }}</div>
                            <div class="fs-3xs text-muted text-uppercase fw-bold">{{ __('Review') }}</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold fs-5 text-success">{{ $stats['completed'] }}</div>
                            <div class="fs-3xs text-muted text-uppercase fw-bold">{{ __('Done') }}</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold fs-5 text-danger">{{ $stats['overdue'] }}</div>
                            <div class="fs-3xs text-muted text-uppercase fw-bold">{{ __('Overdue') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card panel-card border-0 h-100">
                <div class="card-header bg-transparent border-0 pt-3 pb-1">
                    <h6 class="fw-bold mb-0 d-flex align-items-center">
                        <span class="panel-icon panel-icon-primary me-2"><i class="fa-solid fa-users"></i></span>
                        {{ __('Team & Collaboration') }}
                    </h6>
                </div>
                <div class="card-body pt-2">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @forelse($project->members as $member)
                            <span class="member-chip">
                                <span class="avatar-stack"><span class="avatar-chip">{{ Str::substr($member->name, 0, 1) }}</span></span>
                                {{ $member->name }}
                                @if($member->pivot->role === 'manager')
                                    <i class="fa-solid fa-star text-warning fs-3xs"></i>
                                @endif
                            </span>
                        @empty
                            <span class="text-muted small">{{ __('No collaborators assigned yet.') }}</span>
                        @endforelse
                    </div>
                    <hr>
                    <h6 class="fw-bold fs-xs text-uppercase text-muted mb-2">{{ __('Recent Tickets') }}</h6>
                    <div class="d-flex flex-column gap-2">
                        @forelse($project->tickets as $ticket)
                            @php $tColor = $ticket->statusColorMap[$ticket->status] ?? 'secondary'; @endphp
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="d-flex justify-content-between align-items-center text-decoration-none">
                                <span class="ticket-number-chip">{{ $ticket->ticket_number }}</span>
                                <span class="badge bg-soft-{{ $tColor }} text-{{ $tColor }} rounded-pill px-2 fs-3xs">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
                            </a>
                        @empty
                            <span class="text-muted small">{{ __('No tickets raised for this project yet.') }}</span>
                        @endforelse
                    </div>
                    <button class="btn btn-sm btn-light rounded-pill w-100 mt-3" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                        <i class="fa-solid fa-ticket me-1"></i>{{ __('Raise a Ticket for this Project') }}
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Kanban board --}}
    <section class="row mt-3">
        <div class="col-12">
            <div class="card panel-card border-0">
                <div class="card-header bg-transparent border-0 pt-4 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold">{{ __('Task Board') }}</h5>
                    <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#newTaskModal">
                        <i class="fa-solid fa-plus me-1"></i>{{ __('Add Task') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="kanban-board" id="kanban-board" data-task-status-url="{{ url('/tasks') }}">
                        @foreach(['todo' => __('To Do'), 'in_progress' => __('In Progress'), 'review' => __('In Review'), 'completed' => __('Completed')] as $statusKey => $statusLabel)
                            <div class="kanban-column" data-status="{{ $statusKey }}">
                                <div class="kanban-column-head">
                                    <span class="kanban-column-title">{{ $statusLabel }}</span>
                                    <span class="kanban-count">{{ ($tasksByStatus[$statusKey] ?? collect())->count() }}</span>
                                </div>
                                <div class="kanban-column-body" data-column-body>
                                    @foreach(($tasksByStatus[$statusKey] ?? collect()) as $task)
                                        @php $pColor = $task->priorityColorMap[$task->priority] ?? 'secondary'; @endphp
                                        <div class="kanban-card" draggable="true" data-task-id="{{ $task->id }}"
                                             data-bs-toggle="modal" data-bs-target="#taskDetailModal-{{ $task->id }}">
                                            <div class="kanban-card-title">{{ $task->title }}</div>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="fs-3xs text-{{ $pColor }} fw-bold">
                                                    <span class="kanban-priority-dot bg-{{ $pColor }}"></span>{{ ucfirst($task->priority) }}
                                                </span>
                                                <span class="fs-3xs text-muted">{{ $task->progress }}%</span>
                                            </div>
                                            <div class="ptm-progress mt-1" style="height:5px;">
                                                <div class="ptm-progress-bar {{ $task->progress >= 100 ? 'is-complete' : '' }}" style="width: {{ $task->progress }}%"></div>
                                            </div>
                                            <div class="kanban-card-footer">
                                                <span class="kanban-due {{ $task->isOverdue() ? 'is-overdue' : '' }}">
                                                    <i class="fa-regular fa-clock me-1"></i>{{ $task->due_date ? $task->due_date->format('d M') : __('No due date') }}
                                                </span>
                                                @if($task->assignee)
                                                    <span class="kanban-assignee-chip" title="{{ $task->assignee->name }}">{{ Str::substr($task->assignee->name, 0, 1) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="add-task-trigger" data-bs-toggle="modal" data-bs-target="#newTaskModal" data-default-status="{{ $statusKey }}">
                                        <i class="fa-solid fa-plus me-1"></i>{{ __('Add task') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- New Task Modal --}}
    <div class="modal fade" id="newTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0">
                <form method="POST" action="{{ route('projects.tasks.store', $project->id) }}">
                    @csrf
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">{{ __('Add New Task') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Task Title') }}</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Description') }}</label>
                            <textarea name="description" rows="2" class="form-control"></textarea>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-semibold">{{ __('Assign To') }}</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">{{ __('Priority') }}</label>
                                <select name="priority" class="form-select">
                                    @foreach(['low','medium','high','urgent'] as $p)
                                        <option value="{{ $p }}" @selected($p === 'medium')>{{ ucfirst($p) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">{{ __('Start Date') }}</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">{{ __('Due Date') }}</label>
                                <input type="date" name="due_date" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">{{ __('Estimated Hours') }}</label>
                                <input type="number" step="0.5" name="estimated_hours" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-3">{{ __('Add Task') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- New Ticket Modal (project-scoped) --}}
    <div class="modal fade" id="newTicketModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0">
                <form method="POST" action="{{ route('tickets.store') }}">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">{{ __('Raise Ticket') }} — {{ $project->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Subject') }}</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Description') }}</label>
                            <textarea name="description" rows="3" class="form-control" required></textarea>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-semibold">{{ __('Category') }}</label>
                                <select name="category" class="form-select">
                                    @foreach(['bug','support','feature_request','other'] as $c)
                                        <option value="{{ $c }}">{{ ucfirst(str_replace('_',' ',$c)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">{{ __('Priority') }}</label>
                                <select name="priority" class="form-select">
                                    @foreach(['low','medium','high','critical'] as $p)
                                        <option value="{{ $p }}" @selected($p === 'medium')>{{ ucfirst($p) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('Assign To') }}</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-3"><i class="fa-solid fa-ticket me-1"></i>{{ __('Generate Ticket') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Task Detail Modals (progress, comments, collaboration) --}}
    @foreach($project->tasks as $task)
        <div class="modal fade" id="taskDetailModal-{{ $task->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content rounded-4 border-0">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">{{ $task->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">{{ $task->description ?: __('No description provided.') }}</p>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-xs text-uppercase text-muted">{{ __('Update Progress') }}</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="range" min="0" max="100" value="{{ $task->progress }}" class="form-range task-progress-slider" data-task-id="{{ $task->id }}">
                                    <span class="fw-bold fs-xs task-progress-value" style="min-width:38px;">{{ $task->progress }}%</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-xs text-uppercase text-muted">{{ __('Status') }}</label>
                                <select class="form-select form-select-sm task-status-select" data-task-id="{{ $task->id }}">
                                    @foreach(['todo' => __('To Do'), 'in_progress' => __('In Progress'), 'review' => __('In Review'), 'completed' => __('Completed')] as $sk => $sl)
                                        <option value="{{ $sk }}" @selected($task->status === $sk)>{{ $sl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold fs-xs text-uppercase text-muted mb-2"><i class="fa-solid fa-clock-rotate-left me-1"></i>{{ __('Activity Log') }}</h6>
                        <div class="activity-feed mb-3">
                            @forelse($task->statusLogs as $log)
                                <div class="comment-item">
                                    <div class="comment-avatar">{{ $log->user ? Str::substr($log->user->name,0,1) : '?' }}</div>
                                    <div class="flex-fill">
                                        <div class="small">
                                            <strong>{{ $log->user->name ?? __('System') }}</strong>
                                            {{ __('changed status from') }} <em>{{ $log->old_status }}</em> {{ __('to') }} <strong>{{ $log->new_status }}</strong>
                                        </div>
                                        <div class="fs-3xs text-muted">{{ $log->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small">{{ __('No activity recorded yet.') }}</p>
                            @endforelse
                        </div>

                        <h6 class="fw-bold fs-xs text-uppercase text-muted mb-2"><i class="fa-solid fa-comments me-1"></i>{{ __('Team Comments') }}</h6>
                        <div class="activity-feed mb-3">
                            @forelse($task->comments as $comment)
                                <div class="comment-item">
                                    <div class="comment-avatar">{{ Str::substr($comment->user->name,0,1) }}</div>
                                    <div class="flex-fill">
                                        <div class="small"><strong>{{ $comment->user->name }}</strong></div>
                                        <div class="small text-muted">{{ $comment->comment }}</div>
                                        <div class="fs-3xs text-muted">{{ $comment->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small">{{ __('No comments yet — start the discussion!') }}</p>
                            @endforelse
                        </div>

                        <form method="POST" action="{{ route('tasks.comments.store', $task->id) }}" class="d-flex gap-2">
                            @csrf
                            <input type="text" name="comment" class="form-control" placeholder="{{ __('Write a comment...') }}" required>
                            <button type="submit" class="btn btn-primary rounded-pill px-3"><i class="fa-solid fa-paper-plane"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@section('script')
    @include('partials.ptm-styles')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // --- Kanban drag & drop ---
            const board = document.getElementById('kanban-board');
            if (board) {
                let draggedCard = null;

                board.querySelectorAll('.kanban-card').forEach(card => {
                    card.addEventListener('dragstart', function () {
                        draggedCard = this;
                        setTimeout(() => this.classList.add('dragging'), 0);
                    });
                    card.addEventListener('dragend', function () {
                        this.classList.remove('dragging');
                        draggedCard = null;
                    });
                });

                board.querySelectorAll('.kanban-column').forEach(column => {
                    column.addEventListener('dragover', function (e) {
                        e.preventDefault();
                        this.classList.add('drag-over');
                    });
                    column.addEventListener('dragleave', function () {
                        this.classList.remove('drag-over');
                    });
                    column.addEventListener('drop', function (e) {
                        e.preventDefault();
                        this.classList.remove('drag-over');
                        if (!draggedCard) return;

                        const body = this.querySelector('[data-column-body]');
                        const addTrigger = body.querySelector('.add-task-trigger');
                        body.insertBefore(draggedCard, addTrigger);

                        const newStatus = this.getAttribute('data-status');
                        const taskId = draggedCard.getAttribute('data-task-id');

                        fetch(`{{ url('/tasks') }}/${taskId}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ status: newStatus }),
                        }).then(r => r.json()).then(data => {
                            if (data.success) location.reload();
                        }).catch(() => location.reload());
                    });
                });
            }

            // --- Progress slider inside task modal ---
            document.querySelectorAll('.task-progress-slider').forEach(slider => {
                slider.addEventListener('input', function () {
                    const modal = this.closest('.modal-body');
                    modal.querySelector('.task-progress-value').textContent = this.value + '%';
                });
                slider.addEventListener('change', function () {
                    const taskId = this.getAttribute('data-task-id');
                    fetch(`{{ url('/tasks') }}/${taskId}/progress`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ progress: parseInt(this.value, 10) }),
                    }).then(r => r.json()).then(() => location.reload());
                });
            });

            // --- Status select inside task modal ---
            document.querySelectorAll('.task-status-select').forEach(select => {
                select.addEventListener('change', function () {
                    const taskId = this.getAttribute('data-task-id');
                    fetch(`{{ url('/tasks') }}/${taskId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ status: this.value }),
                    }).then(r => r.json()).then(() => location.reload());
                });
            });
        });
    </script>
@endsection

@extends('layouts.admin')

@section('title')
    {{ $ticket->ticket_number }}
@endsection

@section('header')
    <h1 class="h3 mb-3"><strong>Ticket</strong> {{ $ticket->ticket_number }}</h1>
@endsection

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $sColor = $ticket->statusColorMap[$ticket->status] ?? 'secondary';
        $pColor = $ticket->priorityColorMap[$ticket->priority] ?? 'secondary';
    @endphp

    <section class="row g-3">
        <div class="col-lg-8">
            <div class="card panel-card border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <span class="ticket-number-chip">{{ $ticket->ticket_number }}</span>
                            <h4 class="fw-bold mt-2 mb-1">{{ $ticket->subject }}</h4>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-soft-{{ $sColor }} text-{{ $sColor }} rounded-pill px-2">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
                                <span class="badge bg-soft-{{ $pColor }} text-{{ $pColor }} rounded-pill px-2">{{ ucfirst($ticket->priority) }}</span>
                                <span class="badge bg-light text-secondary border rounded-pill px-2">{{ ucfirst(str_replace('_',' ',$ticket->category)) }}</span>
                                @if($ticket->project)
                                    <a href="{{ route('projects.show', $ticket->project->id) }}" class="badge bg-soft-primary text-primary rounded-pill px-2 text-decoration-none">
                                        <i class="fa-solid fa-diagram-project me-1"></i>{{ $ticket->project->title }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <p class="text-muted mt-3">{{ $ticket->description }}</p>

                    <hr>
                    <h6 class="fw-bold fs-xs text-uppercase text-muted mb-3"><i class="fa-solid fa-comments me-1"></i>{{ __('Conversation') }}</h6>
                    <div class="activity-feed mb-3">
                        @forelse($ticket->replies as $reply)
                            <div class="comment-item">
                                <div class="comment-avatar">{{ Str::substr($reply->user->name,0,1) }}</div>
                                <div class="flex-fill">
                                    <div class="small d-flex align-items-center gap-2">
                                        <strong>{{ $reply->user->name }}</strong>
                                        @if($reply->is_internal)
                                            <span class="badge bg-soft-warning text-warning fs-3xs">{{ __('Internal note') }}</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted">{{ $reply->message }}</div>
                                    <div class="fs-3xs text-muted">{{ $reply->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small">{{ __('No replies yet.') }}</p>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('tickets.reply', $ticket->id) }}">
                        @csrf
                        <textarea name="message" rows="3" class="form-control mb-2" placeholder="{{ __('Write a reply...') }}" required></textarea>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_internal" value="1" id="isInternal">
                                <label class="form-check-label small text-muted" for="isInternal">{{ __('Internal note (not visible to requester)') }}</label>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fa-solid fa-paper-plane me-1"></i>{{ __('Send Reply') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card panel-card border-0 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold fs-xs text-uppercase text-muted mb-3">{{ __('Ticket Details') }}</h6>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">{{ __('Raised by') }}</span>
                        <span class="fw-semibold">{{ $ticket->raisedBy->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">{{ __('Created') }}</span>
                        <span class="fw-semibold">{{ $ticket->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    @if($ticket->resolved_at)
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted">{{ __('Resolved') }}</span>
                            <span class="fw-semibold">{{ $ticket->resolved_at->format('d M Y, h:i A') }}</span>
                        </div>
                    @endif

                    <hr>
                    <form method="POST" action="{{ route('tickets.assign', $ticket->id) }}" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <label class="form-label fw-semibold fs-xs text-uppercase text-muted">{{ __('Assigned To') }}</label>
                        <select name="assigned_to" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">{{ __('Unassigned') }}</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" @selected($ticket->assigned_to == $emp->id)>{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </form>

                    <form method="POST" action="{{ route('tickets.status', $ticket->id) }}">
                        @csrf
                        @method('PATCH')
                        <label class="form-label fw-semibold fs-xs text-uppercase text-muted">{{ __('Status') }}</label>
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach(['open','in_progress','resolved','closed'] as $s)
                                <option value="{{ $s }}" @selected($ticket->status === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <form method="POST" action="{{ route('tickets.destroy', $ticket->id) }}" onsubmit="return confirm('{{ __('Delete this ticket permanently?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill"><i class="fa-solid fa-trash me-1"></i>{{ __('Delete Ticket') }}</button>
            </form>
        </div>
    </section>
@endsection

@section('script')
    @include('partials.ptm-styles')
@endsection

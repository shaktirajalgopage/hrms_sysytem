@extends('layouts.admin')

@section('title')
    {{ __('Support Tickets') }}
@endsection

@section('header')
    <h1 class="h3 mb-3"><strong>Ticket</strong> Management</h1>
@endsection

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <section class="row g-3">
        <div class="col-6 col-xl-3">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div><p class="stat-label mb-1">{{ __('Open') }}</p><h2 class="stat-number mb-0">{{ $stats['open'] }}</h2></div>
                        <div class="stat-icon stat-icon-danger"><i class="fa-solid fa-envelope-open-text"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div><p class="stat-label mb-1">{{ __('In Progress') }}</p><h2 class="stat-number mb-0">{{ $stats['in_progress'] }}</h2></div>
                        <div class="stat-icon stat-icon-warning"><i class="fa-solid fa-gears"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div><p class="stat-label mb-1">{{ __('Resolved') }}</p><h2 class="stat-number mb-0">{{ $stats['resolved'] }}</h2></div>
                        <div class="stat-icon stat-icon-info"><i class="fa-solid fa-check-double"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div><p class="stat-label mb-1">{{ __('Closed') }}</p><h2 class="stat-number mb-0">{{ $stats['closed'] }}</h2></div>
                        <div class="stat-icon stat-icon-success"><i class="fa-solid fa-box-archive"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="row mt-3">
        <div class="col-12">
            <div class="card panel-card border-0">
                <div class="card-header bg-transparent border-0 pt-4 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold">{{ __('All Tickets') }}</h5>
                    <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                        <i class="fa-solid fa-plus me-1"></i>{{ __('New Ticket') }}
                    </button>
                </div>
                <div class="card-body pt-2">
                    <form method="GET" class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search ticket # or subject...') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">{{ __('All Statuses') }}</option>
                                @foreach(['open','in_progress','resolved','closed'] as $s)
                                    <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="priority" class="form-select" onchange="this.form.submit()">
                                <option value="">{{ __('All Priorities') }}</option>
                                @foreach(['low','medium','high','critical'] as $p)
                                    <option value="{{ $p }}" @selected(request('priority') == $p)>{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-light w-100" type="submit"><i class="fa-solid fa-filter me-1"></i>{{ __('Filter') }}</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 modern-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Ticket #') }}</th>
                                    <th>{{ __('Subject') }}</th>
                                    <th>{{ __('Project') }}</th>
                                    <th>{{ __('Raised By') }}</th>
                                    <th>{{ __('Assigned To') }}</th>
                                    <th>{{ __('Priority') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    @php
                                        $sColor = $ticket->statusColorMap[$ticket->status] ?? 'secondary';
                                        $pColor = $ticket->priorityColorMap[$ticket->priority] ?? 'secondary';
                                    @endphp
                                    <tr>
                                        <td><a href="{{ route('tickets.show', $ticket->id) }}" class="ticket-number-chip text-decoration-none">{{ $ticket->ticket_number }}</a></td>
                                        <td><a href="{{ route('tickets.show', $ticket->id) }}" class="text-dark fw-bold text-decoration-none">{{ Str::limit($ticket->subject, 40) }}</a></td>
                                        <td class="text-muted small">{{ $ticket->project->title ?? __('General') }}</td>
                                        <td class="text-muted small">{{ $ticket->raisedBy->name ?? '—' }}</td>
                                        <td class="text-muted small">{{ $ticket->assignee->name ?? __('Unassigned') }}</td>
                                        <td><span class="badge bg-soft-{{ $pColor }} text-{{ $pColor }} rounded-pill px-2">{{ ucfirst($ticket->priority) }}</span></td>
                                        <td><span class="badge bg-soft-{{ $sColor }} text-{{ $sColor }} rounded-pill px-2">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span></td>
                                        <td class="text-muted small">{{ $ticket->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center py-4 text-muted small">{{ __('No tickets found.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $tickets->links() }}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- New Ticket Modal --}}
    <div class="modal fade" id="newTicketModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 border-0">
                <form method="POST" action="{{ route('tickets.store') }}">
                    @csrf
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">{{ __('Generate New Ticket') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Project (optional)') }}</label>
                            <select name="project_id" class="form-select">
                                <option value="">{{ __('General / Not project-specific') }}</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}">{{ $p->title }}</option>
                                @endforeach
                            </select>
                        </div>
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

@endsection

@section('script')
    @include('partials.ptm-styles')
@endsection

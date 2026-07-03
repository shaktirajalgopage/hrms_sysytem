@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp

@section('title')
    {{ __('Interview Pipeline') }}
@endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3">{{ __('Interview Pipeline') }}</h1>
    <div class="d-flex gap-2">
      <a href="{{ route($routePrefix . 'kanban') }}" class="btn btn-outline-secondary">
        <i class="fas fa-columns"></i>
        <span class="ps-1">{{ __('Kanban View') }}</span>
      </a>
      <a href="{{ route($routePrefix . 'applications.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        <span class="ps-1">{{ __('Add Candidate') }}</span>
      </a>
    </div>
  </div>
@endsection

@section('content')

{{-- Stats Row --}}
<div class="row mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0"><h5 class="card-title">Total Applications</h5></div>
                    <div class="col-auto"><div class="stat text-primary"><i class="fas fa-file-alt"></i></div></div>
                </div>
                <h1 class="mt-1 mb-3">{{ $stats['total'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0"><h5 class="card-title">Active</h5></div>
                    <div class="col-auto"><div class="stat text-success"><i class="fas fa-user-clock"></i></div></div>
                </div>
                <h1 class="mt-1 mb-3">{{ $stats['active'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0"><h5 class="card-title">Hired</h5></div>
                    <div class="col-auto"><div class="stat text-success"><i class="fas fa-check-circle"></i></div></div>
                </div>
                <h1 class="mt-1 mb-3">{{ $stats['hired'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0"><h5 class="card-title">This Week</h5></div>
                    <div class="col-auto"><div class="stat text-info"><i class="fas fa-calendar-week"></i></div></div>
                </div>
                <h1 class="mt-1 mb-3">{{ $stats['this_week'] }}</h1>
            </div>
        </div>
    </div>
</div>

{{-- Pipeline Stage Summary --}}
<div class="row mb-4">
    @foreach($stages as $key => $stage)
        @if($key !== 'rejected')
        <div class="col">
            <a href="{{ route($routePrefix . 'applications.index', ['stage' => $key]) }}"
               class="card text-decoration-none {{ request('stage') === $key ? 'border-primary' : '' }}">
                <div class="card-body py-2 px-3 text-center">
                    <div class="small text-muted">{{ $stage['label'] }}</div>
                    <div class="fw-bold">
                        {{ $applications->where('stage', $key)->count() }}
                    </div>
                </div>
            </a>
        </div>
        @endif
    @endforeach
</div>

{{-- Filter + Table --}}
<section class="row">
    <div class="col-12">
        <div class="card flex-fill">
            <div class="card-header bg-white border-bottom py-3">
                <form action="{{ route($routePrefix . 'applications.index') }}" method="GET" class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control" placeholder="Search name or email...">
                    </div>
                    <div class="col-md-3">
                        <select name="stage" class="form-select">
                            <option value="">-- All Stages --</option>
                            @foreach($stages as $key => $stage)
                                <option value="{{ $key }}" {{ request('stage') === $key ? 'selected' : '' }}>
                                    {{ $stage['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="position" class="form-select">
                            <option value="">-- All Positions --</option>
                            @foreach($positions as $id => $title)
                                <option value="{{ $id }}" {{ request('position') == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">-- All Statuses --</option>
                            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
                            <option value="on_hold"   {{ request('status') === 'on_hold'   ? 'selected' : '' }}>On Hold</option>
                            <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover my-0 align-middle">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Position</th>
                            <th>Stage</th>
                            <th>Rounds</th>
                            <th>Source</th>
                            <th>Assigned To</th>
                            <th>Applied</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-initials bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                             style="width:38px;height:38px;font-size:13px;flex-shrink:0">
                                            {{ $app->candidate->initials }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $app->candidate->name }}</div>
                                            <small class="text-muted">{{ $app->candidate->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $app->jobPosition->title }}</div>
                                    <small class="text-muted">{{ $app->jobPosition->department }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $app->stageBadgeColor }}">
                                        {{ $app->stageLabel }}
                                    </span>
                                </td>
                                <td>
                                    @php $rounds = $app->interviewRounds; @endphp
                                    @if($rounds->count())
                                        <div class="d-flex gap-1">
                                            @foreach($rounds as $round)
                                                <span class="badge bg-{{ \App\Models\InterviewRound::outcomes()[$round->outcome]['color'] ?? 'secondary' }}"
                                                      title="{{ $round->round_name }}">
                                                    {{ $loop->iteration }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <small class="text-muted">None yet</small>
                                    @endif
                                </td>
                                <td>
                                    @if($app->source)
                                        <span class="badge bg-light text-dark border">
                                            {{ \App\Models\Application::sources()[$app->source] ?? $app->source }}
                                        </span>
                                    @else
                                        <small class="text-muted">—</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $app->assignedTo->name ?? '<span class="text-muted">Unassigned</span>' }}
                                </td>
                                <td>
                                    <small class="text-muted">{{ $app->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route($routePrefix . 'applications.show', $app) }}"
                                           class="btn btn-sm btn-light border" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($app->candidate->hasCv())
                                            <a href="{{ route($routePrefix . 'cv.download', $app) }}"
                                               class="btn btn-sm btn-light border text-info" title="Download CV">
                                                <i class="fas fa-file-download"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route($routePrefix . 'applications.edit', $app) }}"
                                           class="btn btn-sm btn-light border text-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route($routePrefix . 'applications.destroy', $app) }}"
                                              method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger"
                                                    title="Delete"
                                                    onclick="return confirm('Delete this application?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No applications found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($applications->hasPages())
                <div class="card-footer bg-white border-top">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

@endsection

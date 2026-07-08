@extends('layouts.admin')

@section('title')
    {{ __('Projects') }}
@endsection

@section('header')
    <h1 class="h3 mb-3"><strong>Project</strong> Management</h1>
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
                        <div>
                            <p class="stat-label mb-1">{{ __('Total Projects') }}</p>
                            <h2 class="stat-number mb-0">{{ $stats['total'] }}</h2>
                        </div>
                        <div class="stat-icon stat-icon-primary"><i class="fa-solid fa-diagram-project"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="stat-label mb-1">{{ __('Active') }}</p>
                            <h2 class="stat-number mb-0">{{ $stats['active'] }}</h2>
                        </div>
                        <div class="stat-icon stat-icon-success"><i class="fa-solid fa-bolt"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="stat-label mb-1">{{ __('On Hold') }}</p>
                            <h2 class="stat-number mb-0">{{ $stats['on_hold'] }}</h2>
                        </div>
                        <div class="stat-icon stat-icon-warning"><i class="fa-solid fa-pause"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="stat-label mb-1">{{ __('Completed') }}</p>
                            <h2 class="stat-number mb-0">{{ $stats['completed'] }}</h2>
                        </div>
                        <div class="stat-icon stat-icon-teal"><i class="fa-solid fa-circle-check"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="row mt-3">
        <div class="col-12">
            <div class="card panel-card border-0">
                <div class="card-body">
                    <form method="GET" class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search project, code or client...') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">{{ __('All Statuses') }}</option>
                                @foreach(['planning','active','on_hold','completed','cancelled'] as $s)
                                    <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-light w-100" type="submit"><i class="fa-solid fa-filter me-1"></i>{{ __('Filter') }}</button>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <a href="{{ route('projects.create') }}" class="btn btn-primary rounded-pill px-3">
                                <i class="fa-solid fa-plus me-1"></i>{{ __('New Project') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="row mt-1 g-3">
        @forelse($projects as $project)
            @php
                $statusColor = $project->statusColorMap[$project->status] ?? 'secondary';
                $priorityColor = $project->priorityColorMap[$project->priority] ?? 'secondary';
            @endphp
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('projects.show', $project->id) }}" class="project-card">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <span class="project-code">{{ $project->code }}</span>
                        <span class="badge bg-soft-{{ $statusColor }} text-{{ $statusColor }} rounded-pill px-2">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span>
                    </div>
                    <h5 class="project-title">{{ $project->title }}</h5>
                    <p class="text-muted small mb-2">{{ Str::limit($project->description, 80) ?: __('No description provided.') }}</p>

                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="fs-2xs text-muted fw-bold text-uppercase">{{ __('Progress') }}</span>
                        <span class="fs-2xs fw-bold text-dark">{{ $project->progress }}%</span>
                    </div>
                    <div class="ptm-progress mb-3">
                        <div class="ptm-progress-bar {{ $project->progress >= 100 ? 'is-complete' : '' }}" style="width: {{ $project->progress }}%"></div>
                    </div>

                    <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top">
                        <span class="badge bg-soft-{{ $priorityColor }} text-{{ $priorityColor }} rounded-pill px-2 fs-3xs">{{ ucfirst($project->priority) }}</span>
                        <span class="fs-3xs text-muted"><i class="fa-solid fa-list-check me-1"></i>{{ $project->tasks_count }} {{ __('tasks') }}</span>
                        <span class="fs-3xs text-muted">{{ $project->end_date ? $project->end_date->format('d M Y') : __('No deadline') }}</span>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="card panel-card border-0">
                    <div class="card-body">
                        <div class="empty-state">
                            <i class="fa-solid fa-diagram-project"></i>
                            <p class="mb-2">{{ __('No projects found.') }}</p>
                            <a href="{{ route('projects.create') }}" class="btn btn-sm btn-primary rounded-pill">{{ __('Create your first project') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </section>

    <section class="row mt-2">
        <div class="col-12">{{ $projects->links() }}</div>
    </section>
@endsection

@section('script')
    @include('partials.ptm-styles')
@endsection

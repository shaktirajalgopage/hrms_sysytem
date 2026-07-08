@extends('layouts.admin')

@section('title')
    {{ __('Reports & Analytics') }}
@endsection

@section('header')
    <h1 class="h3 mb-3"><strong>Progress & Ticket</strong> Reports</h1>
@endsection

@section('content')

    <section class="row g-3">
        <div class="col-6 col-xl-2">
            <div class="card stat-card border-0"><div class="card-body">
                <p class="stat-label mb-1">{{ __('Projects') }}</p>
                <h2 class="stat-number mb-0">{{ $projectStats['total'] }}</h2>
                <span class="fs-3xs text-muted">{{ __('Avg progress') }} {{ $projectStats['avg_progress'] }}%</span>
            </div></div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card stat-card border-0"><div class="card-body">
                <p class="stat-label mb-1">{{ __('Active') }}</p>
                <h2 class="stat-number mb-0 text-primary">{{ $projectStats['active'] }}</h2>
            </div></div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card stat-card border-0"><div class="card-body">
                <p class="stat-label mb-1">{{ __('Tasks Total') }}</p>
                <h2 class="stat-number mb-0">{{ $taskStats['total'] }}</h2>
                <span class="fs-3xs text-danger">{{ $taskStats['overdue'] }} {{ __('overdue') }}</span>
            </div></div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card stat-card border-0"><div class="card-body">
                <p class="stat-label mb-1">{{ __('Tasks Done') }}</p>
                <h2 class="stat-number mb-0 text-success">{{ $taskStats['completed'] }}</h2>
            </div></div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card stat-card border-0"><div class="card-body">
                <p class="stat-label mb-1">{{ __('Open Tickets') }}</p>
                <h2 class="stat-number mb-0 text-danger">{{ $ticketStats['open'] }}</h2>
            </div></div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="card stat-card border-0"><div class="card-body">
                <p class="stat-label mb-1">{{ __('Critical Open') }}</p>
                <h2 class="stat-number mb-0 text-warning">{{ $ticketStats['critical_open'] }}</h2>
            </div></div>
        </div>
    </section>

    <section class="row mt-3 g-3">
        <div class="col-lg-6">
            <div class="card panel-card border-0">
                <div class="card-header bg-transparent border-0 pt-4"><h5 class="fw-bold mb-0">{{ __('Task Distribution') }}</h5></div>
                <div class="card-body"><canvas id="taskDistributionChart" height="220"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card panel-card border-0">
                <div class="card-header bg-transparent border-0 pt-4"><h5 class="fw-bold mb-0">{{ __('Ticket Trend (Last 6 Months)') }}</h5></div>
                <div class="card-body"><canvas id="ticketTrendChart" height="220"></canvas></div>
            </div>
        </div>
    </section>

    <section class="row mt-3 g-3">
        <div class="col-lg-7">
            <div class="card panel-card border-0">
                <div class="card-header bg-transparent border-0 pt-4"><h5 class="fw-bold mb-0">{{ __('Project-wise Progress') }}</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 modern-table">
                            <thead><tr>
                                <th>{{ __('Project') }}</th><th>{{ __('Tasks') }}</th><th>{{ __('Completed') }}</th><th>{{ __('Progress') }}</th>
                            </tr></thead>
                            <tbody>
                                @foreach($projectProgress as $p)
                                    <tr>
                                        <td class="fw-semibold">{{ $p->title }}</td>
                                        <td class="text-muted small">{{ $p->tasks_count }}</td>
                                        <td class="text-muted small">{{ $p->completed_tasks_count }}</td>
                                        <td style="min-width:140px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="ptm-progress flex-fill"><div class="ptm-progress-bar {{ $p->progress >= 100 ? 'is-complete' : '' }}" style="width:{{ $p->progress }}%"></div></div>
                                                <span class="fs-3xs fw-bold">{{ $p->progress }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card panel-card border-0">
                <div class="card-header bg-transparent border-0 pt-4"><h5 class="fw-bold mb-0">{{ __('Employee Workload & Engagement') }}</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 modern-table">
                            <thead><tr>
                                <th>{{ __('Employee') }}</th><th>{{ __('Open') }}</th><th>{{ __('Completed') }}</th>
                            </tr></thead>
                            <tbody>
                                @forelse($employeeWorkload as $emp)
                                    <tr>
                                        <td class="fw-semibold">{{ $emp->name }}</td>
                                        <td><span class="badge bg-soft-primary text-primary rounded-pill px-2">{{ $emp->open_tasks_count }}</span></td>
                                        <td><span class="badge bg-soft-success text-success rounded-pill px-2">{{ $emp->completed_tasks_count }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted small py-3">{{ __('No task assignments yet.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    @include('partials.ptm-styles')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const taskCtx = document.getElementById('taskDistributionChart');
            new Chart(taskCtx, {
                type: 'doughnut',
                data: {
                    labels: ['{{ __('To Do') }}', '{{ __('In Progress') }}', '{{ __('In Review') }}', '{{ __('Completed') }}'],
                    datasets: [{
                        data: [{{ $taskStats['todo'] }}, {{ $taskStats['in_progress'] }}, {{ $taskStats['review'] }}, {{ $taskStats['completed'] }}],
                        backgroundColor: ['#8a93a3', '#3b7ddd', '#f0ad4e', '#1cbb8c'],
                        borderWidth: 0,
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
            });

            const trendLabels = @json(array_keys($ticketTrend->toArray()));
            const trendData = @json(array_values($ticketTrend->toArray()));

            new Chart(document.getElementById('ticketTrendChart'), {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: '{{ __('Tickets Raised') }}',
                        data: trendData,
                        borderColor: '#3b7ddd',
                        backgroundColor: 'rgba(59,125,221,0.08)',
                        fill: true,
                        tension: 0.35,
                    }]
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
            });
        });
    </script>
@endsection

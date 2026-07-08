@extends('layouts.admin')

@section('title')
    {{ __('Dashboard') }}
@endsection

@section('header')
    <h1 class="h3 mb-3"><strong>Analytics</strong> Dashboard</h1>
@endsection

@section('content')
    <section class="row g-3">
        <div class="col-6 col-xl-3">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="stat-label mb-1">{{ __('Schedules') }}</p>
                            <h2 class="stat-number mb-0">{{ App\Models\Schedule::count() }}</h2>
                        </div>
                        <div class="stat-icon stat-icon-primary">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="stat-label mb-1">{{ __('Departments') }}</p>
                            <h2 class="stat-number mb-0">{{ App\Models\Department::where('status', 1)->count() }}</h2>
                        </div>
                        <div class="stat-icon stat-icon-success">
                            <i class="fa-solid fa-users-gear"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="stat-label mb-1">{{ __('Employees') }}</p>
                            <h2 class="stat-number mb-0">{{ App\Models\Employee::where('status', 1)->count() }}</h2>
                        </div>
                        <div class="stat-icon stat-icon-info">
                            <i class="fa-solid fa-users-viewfinder"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card stat-card border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="stat-label mb-1">{{ __('Users & Members') }}</p>
                            <h2 class="stat-number mb-0">{{ App\Models\User::where('status', 1)->count() }}</h2>
                        </div>
                        <div class="stat-icon stat-icon-warning">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="row mt-3 g-3">
        <div class="col-lg-6 d-flex">
            <div class="card flex-fill w-100 panel-card border-0">
                <div class="card-header bg-transparent border-0 pt-4 pb-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                            <span class="panel-icon panel-icon-amber me-2"><i class="fa-solid fa-bullhorn"></i></span>
                            {{ __('Latest Announcements') }}
                        </h5>
                    </div>
                </div>
                <div class="card-body pt-1">
                    <div class="notification-feed">
                        @php
                            $announcements = App\Models\UserAppNotificastion::sent()->latest('sent_at')->take(5)->get();
                        @endphp
                        @forelse($announcements as $announcement)
                            <div class="announcement-item" data-announcement-toggle role="button" tabindex="0">
                                <div class="announcement-icon bg-soft-{{ $announcement->icon_color_map[$announcement->type] ?? 'primary' }} text-{{ $announcement->icon_color_map[$announcement->type] ?? 'primary' }}">
                                    <i class="{{ $announcement->icon_class }}"></i>
                                </div>
                                <div class="announcement-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-1 fw-semibold text-dark">{{ $announcement->title }}</h6>
                                        <div class="d-flex align-items-center">
                                            <span class="announcement-time">{{ $announcement->sent_at ? $announcement->sent_at->diffForHumans() : '' }}</span>
                                            <i class="fa-solid fa-chevron-down announcement-chevron ms-2"></i>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-2 announcement-preview">{{ Str::limit($announcement->body, 110) }}</p>
                                    <p class="text-muted small mb-2 announcement-full">{{ $announcement->body }}</p>
                                    @if($announcement->action_url)
                                        <a href="{{ $announcement->action_url }}" class="announcement-cta" onclick="event.stopPropagation()" target="_blank">
                                            {{ $announcement->action_label ?? __('View Details') }}
                                            <i class="fa-solid fa-arrow-right-long ms-1"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="fas fa-bullhorn"></i>
                                <p class="mb-0">{{ __('No active announcements posted.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 d-flex">
            <div class="card flex-fill w-100 panel-card border-0 d-flex flex-column justify-content-between overflow-hidden">
                @php
                    $allHolidays = App\Models\Holiday::where('status', true)
                        ->where('start_date', '>=', now()->startOfDay())
                        ->orderBy('start_date', 'asc')
                        ->get();
                    
                    $nextHoliday = $allHolidays->first();
                    $remainingHolidays = $allHolidays->skip(1);

                    $mappedHolidaysArray = [];
                    foreach ($allHolidays as $h) {
                        $daysRem = now()->startOfDay()->diffInDays($h->start_date);
                        $remText = $daysRem === 0 ? __('Today') : ($daysRem === 1 ? __('Tomorrow') : __('In :days Days', array('days' => $daysRem)));
                        
                        $mappedHolidaysArray[] = [
                            'name'          => $h->name,
                            'date'          => $h->start_date->format('Y-m-d'),
                            'day_num'       => $h->start_date->format('d'),
                            'month_name'    => $h->start_date->format('M'),
                            'year'          => $h->start_date->format('Y'),
                            'day_of_week'   => $h->start_date->format('l'),
                            'duration_text' => $h->no_of_days . ' ' . Str::plural('Day', $h->no_of_days),
                            'date_range'    => $h->no_of_days > 1 ? $h->start_date->format('d M') . ' - ' . $h->end_date->format('d M, Y') : $h->start_date->format('d F Y'),
                            'countdown'     => $remText
                        ];
                    }
                @endphp

                <div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                                <span class="panel-icon panel-icon-teal me-2"><i class="fa-solid fa-umbrella-beach"></i></span>
                                {{ __('Holidays & Time Off') }}
                            </h5>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="row g-3">
                            <div class="col-md-6">
                                @if($nextHoliday)
                                    @php
                                        $daysRemaining = now()->startOfDay()->diffInDays($nextHoliday->start_date);
                                        $remainingText = $daysRemaining === 0 ? __('Today') : ($daysRemaining === 1 ? __('Tomorrow') : __('In :days Days', array('days' => $daysRemaining)));
                                    @endphp
                                    <div class="holiday-hero-card h-100 active" 
                                         id="hero-holiday-card"
                                         role="button"
                                         data-holiday-date="{{ $nextHoliday->start_date->format('Y-m-d') }}">
                                        
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <span class="badge bg-soft-teal text-teal rounded-pill px-2.5 py-1 fw-bold fs-xs" id="hero-badge-label">{{ __('Next Upcoming') }}</span>
                                            <span class="holiday-countdown-tag" id="hero-countdown">{{ $remainingText }}</span>
                                        </div>
                                        
                                        <h4 class="fw-bold text-dark mb-1 holiday-hero-title" id="hero-title">{{ $nextHoliday->name }}</h4>
                                        <p class="text-muted small mb-3" id="hero-day-of-week">
                                            {{ $nextHoliday->start_date->format('l') }}
                                        </p>
                                        
                                        <div class="mt-auto d-flex align-items-center justify-content-between border-top border-light-subtle pt-3">
                                            <div class="d-flex align-items-center">
                                                <div class="hero-date-square me-2">
                                                    <span class="d-block fw-bold fs-5" id="hero-date-day">{{ $nextHoliday->start_date->format('d') }}</span>
                                                    <span class="text-uppercase tracking-wider font-monospace fs-2xs text-muted" id="hero-date-month">{{ $nextHoliday->start_date->format('M') }}</span>
                                                </div>
                                                <div class="lh-sm">
                                                    <span class="text-dark fw-semibold d-block small" id="hero-date-range">
                                                        @if($nextHoliday->no_of_days > 1)
                                                            {{ $nextHoliday->start_date->format('d M') }} - {{ $nextHoliday->end_date->format('d M, Y') }}
                                                        @else
                                                            {{ $nextHoliday->start_date->format('d F Y') }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 font-monospace fs-2xs" id="hero-duration">
                                                {{ $nextHoliday->no_of_days }} {{ Str::plural('Day', $nextHoliday->no_of_days) }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="empty-state border rounded-4 bg-light-soft h-100 d-flex flex-column align-items-center justify-content-center p-4">
                                        <i class="fas fa-umbrella-beach text-muted mb-2"></i>
                                        <p class="mb-0 small fw-medium text-secondary">{{ __('No upcoming holidays scheduled.') }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <div class="mini-calendar h-100 d-flex flex-column justify-content-between">
                                    <div class="mini-calendar-head d-flex align-items-center justify-content-between mb-2">
                                        <button type="button" class="btn btn-icon-nav" id="prev-month-btn"><i class="fa-solid fa-chevron-left"></i></button>
                                        <span class="fw-bold text-dark font-monospace" id="calendar-month-label"></span>
                                        <button type="button" class="btn btn-icon-nav" id="next-month-btn"><i class="fa-solid fa-chevron-right"></i></button>
                                    </div>
                                    <div class="mini-calendar-grid" id="calendar-days-container">
                                        </div>
                                </div>
                            </div>
                        </div>

                        @if($remainingHolidays->count() > 0)
                            <div class="collapse-timeline-wrapper mt-3">
                                <div class="collapse" id="allHolidaysCollapse">
                                    <div class="pt-2 pb-1 font-monospace text-muted fs-2xs text-uppercase tracking-wider border-bottom mb-2">{{ __('Remaining Schedule') }}</div>
                                    <div class="holiday-timeline-feed">
                                        @foreach($remainingHolidays as $holiday)
                                            @php
                                                $remDays = now()->startOfDay()->diffInDays($holiday->start_date);
                                            @endphp
                                            <div class="timeline-holiday-item" 
                                                 data-holiday-date="{{ $holiday->start_date->format('Y-m-d') }}"
                                                 role="button">
                                                <div class="timeline-left">
                                                    <div class="timeline-dot"></div>
                                                    <div class="timeline-badge">
                                                        <span class="d-block fw-bold text-dark">{{ $holiday->start_date->format('d') }}</span>
                                                        <span class="text-uppercase text-muted text-center font-monospace d-block">{{ $holiday->start_date->format('M') }}</span>
                                                    </div>
                                                </div>
                                                <div class="timeline-body">
                                                    <div class="d-flex justify-content-between align-items-start w-100">
                                                        <div>
                                                            <h6 class="mb-0 text-dark fw-bold timeline-title">{{ $holiday->name }}</h6>
                                                            <span class="text-muted fs-2xs font-monospace">
                                                                {{ $holiday->start_date->format('l') }} • 
                                                                @if($holiday->no_of_days > 1)
                                                                    {{ $holiday->no_of_days }} {{ __('days duration') }}
                                                                @else
                                                                    {{ __('Single day') }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <span class="badge bg-light text-muted font-monospace border rounded-pill fs-3xs">In {{ $remDays }}d</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($remainingHolidays->count() > 0)
                    <div class="card-footer bg-transparent border-0 px-4 pb-3 pt-0">
                        <button class="btn btn-view-all-holidays w-100 d-flex align-items-center justify-content-center collapsed" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#allHolidaysCollapse" 
                                aria-expanded="false" 
                                aria-controls="allHolidaysCollapse"
                                id="toggle-holidays-btn">
                            <span>{{ __('View All System Holidays') }}</span>
                            <i class="fa-solid fa-chevron-down ms-2 transition-transform"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="row mt-3">
        <div class="col-sm-12">
            <div class="card panel-card flex-fill w-100 border-0">
                <div class="card-header bg-transparent border-0 pt-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-bold">{{ __('Employee Registry') }}</h5>
                        <a href="{{ route('employee.index') }}" class="btn btn-light btn-sm text-primary rounded-pill px-3">
                            <i class="fas fa-eye me-1"></i>{{ __('View All') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 modern-table">
                            <thead>
                                <tr>
                                    <th>{{ __('SL') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th>{{ __('Appointed') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $employees = App\Models\Employee::paginate(4); @endphp
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('employee.edit', $employee->id) }}" class="text-dark fw-bold text-decoration-none">
                                                {{ $employee->firstname . ' ' . $employee->lastname }}
                                            </a>
                                        </td>
                                        <td>{{ $employee->department->title ?? 'N/A' }}</td>
                                        <td>{{ $employee->designation->title ?? 'N/A' }}</td>
                                        <td class="text-muted small">{{ $employee->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="row mt-3 g-3">
        <div class="col-lg-6">
            <div class="card panel-card flex-fill w-100 border-0">
                <div class="card-header bg-transparent border-0 pt-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 fw-bold">{{ __('Recent Users') }}</h5>
                        <a href="{{ route('user.index') }}" class="btn btn-light btn-sm text-primary rounded-pill px-3">
                            <i class="fas fa-eye me-1"></i>{{ __('View All') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 modern-table">
                            <thead>
                                <tr>
                                    <th>{{ __('SL') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Role') }}</th>
                                    <th>{{ __('Joined') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $users = App\Models\User::paginate(4); @endphp
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('user.edit', $user->id) }}" class="text-dark fw-bold text-decoration-none">
                                                {{ $user->name }}
                                            </a>
                                        </td>
                                        <td><span class="badge bg-soft-info text-info rounded-pill px-2">{{ $user->role->title ?? 'User' }}</span></td>
                                        <td class="text-muted small">{{ $user->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card panel-card flex-fill w-100 border-0">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                        <span class="panel-icon panel-icon-pink me-2"><i class="fa-solid fa-cake-candles"></i></span>
                        {{ __("Today's Birthdays") }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 modern-table">
                            <thead>
                                <tr>
                                    <th>{{ __('SL') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Date of Birth') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $birthdays = \Illuminate\Support\Facades\Cache::get('todays_birthdays', collect()); @endphp
                                @forelse ($birthdays as $index => $emp)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $emp['name'] }}</td>
                                        <td class="text-muted small">{{ $emp['email'] }}</td>
                                        <td><span class="badge bg-soft-warning text-warning">{{ \Carbon\Carbon::parse($emp['dob'])->format('d M') }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">{{ __('No birthdays today') }}</td>
                                    </tr>
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
<style>
    :root {
        --c-primary: #3b7ddd;
        --c-success: #1cbb8c;
        --c-info: #17a2b8;
        --c-warning: #f0ad4e;
        --c-danger: #dc3545;
        --c-pink: #e85d9e;
        --c-teal: #0fb5ae;
        --c-amber: #f1a93d;
        --c-ink: #1f2937;
        --c-muted: #8a93a3;
        --c-border: #eef1f6;
        --radius-premium: 16px;
    }

    body { background-color: #f5f7fb; }

    /* Premium Panels Override */
    .panel-card {
        border-radius: var(--radius-premium) !important;
        box-shadow: 0 4px 20px rgba(20, 30, 60, 0.03) !important;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.01) !important;
    }

    /* Stat cards */
    .stat-card {
        position: relative;
        border-radius: var(--radius-premium);
        box-shadow: 0 2px 10px rgba(20, 30, 60, 0.04);
        transition: transform .2s ease, box-shadow .2s ease;
        background: #ffffff;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(20, 30, 60, 0.08);
    }
    .stat-label { color: var(--c-muted); font-size: .8rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
    .stat-number { color: var(--c-ink); font-weight: 700; }
    .stat-link { z-index: 1; }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
    }
    .stat-icon-primary { background: rgba(59,125,221,.12); color: var(--c-primary); }
    .stat-icon-success { background: rgba(28,187,140,.12); color: var(--c-success); }
    .stat-icon-info { background: rgba(23,162,184,.12); color: var(--c-info); }
    .stat-icon-warning { background: rgba(240,173,78,.12); color: var(--c-warning); }

    .panel-icon {
        width: 34px; height: 34px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .85rem;
    }
    .panel-icon-amber { background: rgba(241,169,61,.15); color: var(--c-amber); }
    .panel-icon-teal { background: rgba(15,181,174,.15); color: var(--c-teal); }
    .panel-icon-pink { background: rgba(232,93,158,.15); color: var(--c-pink); }

    /* Announcement feed */
    .notification-feed { max-height: 420px; overflow-y: auto; padding-right: 4px; }
    .notification-feed::-webkit-scrollbar { width: 6px; }
    .notification-feed::-webkit-scrollbar-thumb { background: #dfe4ee; border-radius: 10px; }
    .announcement-item {
        display: flex; gap: 14px;
        padding: 14px;
        margin-bottom: 10px;
        border: 1px solid var(--c-border);
        border-radius: 14px;
        background: #fff;
        cursor: pointer;
        transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease;
    }
    .announcement-item:hover { box-shadow: 0 6px 18px rgba(20,30,60,.06); transform: translateY(-1px); }
    .announcement-item:focus-visible { outline: 2px solid var(--c-primary); outline-offset: 2px; }
    .announcement-item.is-expanded {
        border-color: rgba(59,125,221,.35);
        box-shadow: 0 8px 22px rgba(20,30,60,.08);
        background: #fbfcff;
    }
    .announcement-icon {
        flex: 0 0 auto;
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
    .announcement-body { flex: 1; min-width: 0; }
    .announcement-time { font-size: .72rem; color: var(--c-muted); white-space: nowrap; }
    .announcement-chevron {
        font-size: .68rem; color: var(--c-muted);
        transition: transform .25s ease;
    }
    .announcement-item.is-expanded .announcement-chevron { transform: rotate(180deg); color: var(--c-primary); }
    .announcement-preview { display: block; margin-bottom: 0; }
    .announcement-item.is-expanded .announcement-preview { display: none; }
    .announcement-full {
        display: none;
        line-height: 1.55;
        animation: fadeIn .2s ease;
    }
    .announcement-item.is-expanded .announcement-full { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-2px); } to { opacity: 1; transform: translateY(0); } }
    .announcement-cta {
        font-size: .78rem; font-weight: 600; color: var(--c-primary);
        text-decoration: none;
    }
    .announcement-cta:hover { text-decoration: underline; }

    /* Holiday layout styling */
    .holiday-hero-card {
        background: linear-gradient(135deg, #ffffff 0%, #f9fbfb 100%);
        border: 2px solid var(--c-border);
        border-radius: 14px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }
    .holiday-hero-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(15, 181, 174, 0.06);
    }
    .holiday-hero-card.active {
        border-color: var(--c-teal);
        background: linear-gradient(135deg, #ffffff 0%, rgba(15, 181, 174, 0.02) 100%);
        box-shadow: 0 8px 25px rgba(15, 181, 174, 0.08);
    }
    .holiday-countdown-tag {
        font-size: 0.72rem;
        font-weight: 700;
        color: #fff;
        background-color: var(--c-teal);
        padding: 3px 10px;
        border-radius: 999px;
    }
    .hero-date-square {
        width: 44px;
        height: 44px;
        background: rgba(15, 181, 174, 0.08);
        border-radius: 10px;
        color: var(--c-teal);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    .tracking-wider { letter-spacing: 0.06em; }

    .mini-calendar {
        background: #ffffff;
        border: 1px solid var(--c-border);
        border-radius: 14px;
        padding: 16px;
    }
    .btn-icon-nav {
        background: transparent;
        border: none;
        padding: 4px 8px;
        color: var(--c-muted);
        border-radius: 6px;
        transition: all 0.2s;
    }
    .btn-icon-nav:hover {
        background: #f1f3f7;
        color: var(--c-ink);
    }
    .mini-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
        text-align: center;
    }
    .mini-calendar-dow {
        font-size: 0.65rem;
        font-weight: 700;
        color: var(--c-muted);
        text-transform: uppercase;
        padding-bottom: 6px;
    }
    .mini-calendar-cell {
        font-size: 0.75rem;
        font-weight: 500;
        color: #4b5563;
        height: 28px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        position: relative;
        transition: all 0.2s;
    }
    .mini-calendar-cell:not(.empty):not(.is-muted-month) {
        cursor: pointer;
    }
    .mini-calendar-cell:not(.empty):not(.is-muted-month):hover {
        background: #f1f5f9;
    }
    .mini-calendar-cell.empty { visibility: hidden; }
    .mini-calendar-cell.is-muted-month { color: #d1d5db; }
    .mini-calendar-cell.is-today {
        background: rgba(59, 125, 221, 0.08);
        font-weight: 700;
        color: var(--c-primary);
    }
    
    .mini-calendar-cell.has-holiday::after {
        content: '';
        position: absolute;
        bottom: 3px;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background-color: var(--c-teal);
    }
    .mini-calendar-cell.has-holiday.selected-active {
        background-color: var(--c-teal) !important;
        color: #ffffff !important;
        font-weight: 700;
    }
    .mini-calendar-cell.has-holiday.selected-active::after {
        background-color: #ffffff !important;
    }

    .holiday-timeline-feed {
        display: flex;
        flex-direction: column;
        max-height: 240px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .holiday-timeline-feed::-webkit-scrollbar { width: 4px; }
    .holiday-timeline-feed::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    
    .timeline-holiday-item {
        display: flex;
        gap: 12px;
        padding: 10px;
        border-radius: 10px;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        cursor: pointer;
        margin-bottom: 4px;
    }
    .timeline-holiday-item:hover {
        background: #f8fafc;
        border-color: var(--c-border);
    }
    .timeline-holiday-item.active {
        background-color: rgba(15, 181, 174, 0.02);
        border-color: rgba(15, 181, 174, 0.2);
    }
    .timeline-left {
        display: flex;
        align-items: center;
        position: relative;
    }
    .timeline-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #cbd5e1;
        position: absolute;
        left: -12px;
    }
    .timeline-holiday-item.active .timeline-dot {
        background: var(--c-teal);
        box-shadow: 0 0 0 3px rgba(15, 181, 174, 0.2);
    }
    .timeline-badge {
        width: 38px;
        height: 38px;
        background: #f1f5f9;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        line-height: 1.1;
    }
    .timeline-badge span { font-size: 0.8rem; }
    .timeline-badge span.text-uppercase { font-size: 0.55rem; font-weight: 700; }
    .timeline-body { flex: 1; min-width: 0; }
    .timeline-title { font-size: 0.85rem; }

    .btn-view-all-holidays {
        background: #f8fafc;
        border: 1px solid var(--c-border);
        color: #475569;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 10px;
        transition: all 0.2s ease;
    }
    .btn-view-all-holidays:hover {
        background: #f1f5f9;
        color: var(--c-ink);
    }
    .btn-view-all-holidays:not(.collapsed) i {
        transform: rotate(180deg);
    }

    .fs-2xs { font-size: 0.68rem; }
    .fs-3xs { font-size: 0.62rem; }
    .fs-xs { font-size: 0.75rem; }
    .text-teal { color: var(--c-teal) !important; }
    .bg-soft-teal { background-color: rgba(15, 181, 174, 0.1) !important; }

    .empty-state { text-align: center; padding: 40px 10px; color: var(--c-muted); }
    .empty-state i { font-size: 1.6rem; margin-bottom: 8px; display: block; opacity: .5; }
    .empty-state p { font-size: .85rem; }

    .modern-table thead th {
        font-size: .72rem; text-transform: uppercase; letter-spacing: .03em;
        color: var(--c-muted); font-weight: 700; border-bottom: 1px solid var(--c-border);
    }
    .modern-table tbody tr { border-bottom: 1px solid var(--c-border); }
    .modern-table tbody tr:last-child { border-bottom: none; }

    .bg-soft-primary { background-color: rgba(59, 125, 221, 0.1); }
    .text-primary { color: var(--c-primary) !important; }
    .bg-soft-success { background-color: rgba(28, 187, 140, 0.1); }
    .text-success { color: var(--c-success) !important; }
    .bg-soft-info { background-color: rgba(23, 162, 184, 0.1); }
    .text-info { color: var(--c-info) !important; }
    .bg-soft-warning { background-color: rgba(240, 173, 78, 0.1); }
    .text-warning { color: var(--c-warning) !important; }
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
    .text-danger { color: var(--c-danger) !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Announcement Dropdown Logic ---
        document.querySelectorAll('[data-announcement-toggle]').forEach(function (item) {
            function toggle() { item.classList.toggle('is-expanded'); }
            item.addEventListener('click', toggle);
            item.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); }
            });
        });

        // --- Core HRMS Calendar Engine ---
        const holidayDataset = @json($mappedHolidaysArray);

        let currentNavDate = new Date();
        let targetSelectedDateStr = null;

        if (holidayDataset.length > 0) {
            currentNavDate = new Date(holidayDataset[0].date);
            targetSelectedDateStr = holidayDataset[0].date;
        }

        const monthLabel = document.getElementById('calendar-month-label');
        const daysContainer = document.getElementById('calendar-days-container');
        const prevBtn = document.getElementById('prev-month-btn');
        const nextBtn = document.getElementById('next-month-btn');

        function renderCalendar(viewDate) {
            const year = viewDate.getFullYear();
            const month = viewDate.getMonth();
            
            const monthNames = ["January", "February", "March", "April", "May", "June", 
                                "July", "August", "September", "October", "November", "December"];
            if(monthLabel) monthLabel.textContent = `${monthNames[month]} ${year}`;

            if(!daysContainer) return;
            daysContainer.innerHTML = '';
            
            ['Su','Mo','Tu','We','Th','Fr','Sa'].forEach(dow => {
                const dowDiv = document.createElement('div');
                dowDiv.className = 'mini-calendar-dow';
                dowDiv.textContent = dow;
                daysContainer.appendChild(dowDiv);
            });

            const firstDayIndex = new Date(year, month, 1).getDay();
            const totalMonthDays = new Date(year, month + 1, 0).getDate();
            const todayStr = new Date().toISOString().split('T')[0];

            for (let i = 0; i < firstDayIndex; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'mini-calendar-cell empty';
                daysContainer.appendChild(emptyCell);
            }

            for (let day = 1; day <= totalMonthDays; day++) {
                const cell = document.createElement('div');
                cell.className = 'mini-calendar-cell';
                cell.textContent = day;

                const currentLoopDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                if (currentLoopDateStr === todayStr) {
                    cell.classList.add('is-today');
                }

                const matchedHolidays = holidayDataset.filter(h => h.date === currentLoopDateStr);
                if (matchedHolidays.length > 0) {
                    cell.classList.add('has-holiday');
                    cell.setAttribute('title', matchedHolidays.map(h => h.name).join(', '));
                }

                if (currentLoopDateStr === targetSelectedDateStr) {
                    cell.classList.add('selected-active');
                }

                cell.addEventListener('click', function() {
                    if (matchedHolidays.length > 0) {
                        syncSelectedHolidayState(currentLoopDateStr);
                    }
                });

                daysContainer.appendChild(cell);
            }
        }

        function syncSelectedHolidayState(dateStr) {
            targetSelectedDateStr = dateStr;
            currentNavDate = new Date(dateStr);
            renderCalendar(currentNavDate);

            // Fetch the holiday item details from dataset to refresh the layout fields dynamically
            const activeHoliday = holidayDataset.find(h => h.date === dateStr);

            if (activeHoliday) {
                // Update text elements inside the Hero panel layout structure
                const hTitle = document.getElementById('hero-title');
                const hCountdown = document.getElementById('hero-countdown');
                const hDayOfWeek = document.getElementById('hero-day-of-week');
                const hDateDay = document.getElementById('hero-date-day');
                const hDateMonth = document.getElementById('hero-date-month');
                const hDateRange = document.getElementById('hero-date-range');
                const hDuration = document.getElementById('hero-duration');
                const hBadgeLabel = document.getElementById('hero-badge-label');

                if (hTitle) hTitle.textContent = activeHoliday.name;
                if (hCountdown) hCountdown.textContent = activeHoliday.countdown;
                if (hDayOfWeek) hDayOfWeek.textContent = activeHoliday.day_of_week;
                if (hDateDay) hDateDay.textContent = activeHoliday.day_num;
                if (hDateMonth) hDateMonth.textContent = activeHoliday.month_name;
                if (hDateRange) hDateRange.textContent = activeHoliday.date_range;
                if (hDuration) hDuration.textContent = activeHoliday.duration_text;

                // Dynamically tag context if selected holiday isn't the base next index
                if (hBadgeLabel) {
                    if (holidayDataset.length > 0 && holidayDataset[0].date === dateStr) {
                        hBadgeLabel.textContent = "{{ __('Next Upcoming') }}";
                    } else {
                        hBadgeLabel.textContent = "{{ __('Selected Holiday') }}";
                    }
                }
            }

            const heroCard = document.getElementById('hero-holiday-card');
            if (heroCard) {
                if (heroCard.getAttribute('data-holiday-date') === dateStr) {
                    heroCard.classList.add('active');
                } else {
                    // Stay highlighted if it's currently holding our selection state context
                    heroCard.classList.add('active');
                }
            }

            document.querySelectorAll('.timeline-holiday-item').forEach(item => {
                if (item.getAttribute('data-holiday-date') === dateStr) {
                    item.classList.add('active');
                    item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } else {
                    item.classList.remove('active');
                }
            });
        }

        const heroCardElement = document.getElementById('hero-holiday-card');
        if (heroCardElement) {
            heroCardElement.addEventListener('click', function() {
                const date = this.getAttribute('data-holiday-date');
                syncSelectedHolidayState(date);
            });
        }

        document.querySelectorAll('.timeline-holiday-item').forEach(item => {
            item.addEventListener('click', function() {
                const date = this.getAttribute('data-holiday-date');
                syncSelectedHolidayState(date);
            });
        });

        if(prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentNavDate.setMonth(currentNavDate.getMonth() - 1);
                renderCalendar(currentNavDate);
            });
        }

        if(nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentNavDate.setMonth(currentNavDate.getMonth() + 1);
                renderCalendar(currentNavDate);
            });
        }

        renderCalendar(currentNavDate);
    });
</script>
@endsection
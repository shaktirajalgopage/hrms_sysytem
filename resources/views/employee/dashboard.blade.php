@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">

    <!-- Welcome Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="h3 mb-1 text-gray-800 fw-bold">
                @php
                    $hour = date('H');
                    $greeting = 'Welcome';
                    if ($hour >= 5 && $hour < 12) { $greeting = 'Good Morning'; }
                    elseif ($hour >= 12 && $hour < 17) { $greeting = 'Good Afternoon'; }
                    else { $greeting = 'Good Evening'; }
                @endphp
                {{ $greeting }}, {{ Auth::user()->name ?? 'Team Member' }}!
            </h1>
            <p class="text-muted mb-0">Here is a quick snapshot of your workplace overview for today, {{ now()->format('F j, Y') }}.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span class="badge bg-light text-dark p-2 border">
                <i class="align-middle me-1" data-feather="clock"></i> Last Login: {{ Auth::user()->last_login_at?->diffForHumans() ?? 'Just now' }}
            </span>
        </div>
    </div>

    <hr class="my-4 text-muted opacity-25">

    <!-- Key Metrics Cards -->
    <div class="row g-4 mb-4">
        <!-- Attendance Card -->
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 transition-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="card-title text-muted mb-0">Attendance This Month</h5>
                        <div class="avatar bg-primary-subtle text-primary p-2 rounded">
                            <i class="align-middle" data-feather="calendar"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <h2 class="mb-0 fw-bold me-2">{{ $attendanceCount ?? 0 }}</h2>
                        <span class="text-muted small">Days logged</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaves Card -->
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 transition-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="card-title text-muted mb-0">Leave Balance</h5>
                        <div class="avatar bg-warning-subtle text-warning p-2 rounded">
                            <i class="align-middle" data-feather="file-text"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <h2 class="mb-0 fw-bold me-2">{{ $leaveCount ?? 0 }}</h2>
                        <span class="text-muted small">Remaining days</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assets Card -->
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 transition-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="card-title text-muted mb-0">Assigned Assets</h5>
                        <div class="avatar bg-success-subtle text-success p-2 rounded">
                            <i class="align-middle" data-feather="monitor"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <h2 class="mb-0 fw-bold me-2">{{ $assetCount ?? 0 }}</h2>
                        <span class="text-muted small">Active items</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Section: Dynamic Quick Actions & Context -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white p-4 rounded-3 position-relative overflow-hidden">
                <div class="row align-items-center">
                    <div class="col-md-8 z-index-1">
                        <h4 class="fw-bold mb-2">Need to request time off or check your equipment?</h4>
                        <p class="mb-0 opacity-75">You can manage your tasks, view company updates, and streamline your workflow right from your side navigation panel.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Optional subtle styling tweak for hover animations -->
<style>
    .transition-hover {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .transition-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1); }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1); }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
    .bg-gradient-primary { background: linear-gradient(45deg, #4e73df, #224abe); }
</style>
@endsection
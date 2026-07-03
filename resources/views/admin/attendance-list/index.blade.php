@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp


@section('title')
    {{ __('Attendance Logs') }}
@endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3">{{ __('Attendance Logs') }}</h1>
    <div class="d-flex gap-2">
      <a href="{{ route($routePrefix . 'attendance-list.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-sync-alt"></i>
        <span class="ps-1">{{ __('Refresh') }}</span>
      </a>
    </div>
  </div>
@endsection

@section('content')

{{-- ── Stats Cards ── --}}
<div class="row mb-4">
  <div class="col-sm-6 col-xl-3 mb-3">
    <div class="card flex-fill border-0 shadow-sm">
      <div class="card-body py-3 d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10"
             style="width:46px;height:46px;flex-shrink:0">
          <i class="fas fa-clipboard-list text-primary fs-5"></i>
        </div>
        <div>
          <div class="text-muted" style="font-size:12px;font-weight:600;letter-spacing:.05em;text-transform:uppercase">
            Total Logs
          </div>
          <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total']) }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 mb-3">
    <div class="card flex-fill border-0 shadow-sm">
      <div class="card-body py-3 d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10"
             style="width:46px;height:46px;flex-shrink:0">
          <i class="fas fa-sign-in-alt text-success fs-5"></i>
        </div>
        <div>
          <div class="text-muted" style="font-size:12px;font-weight:600;letter-spacing:.05em;text-transform:uppercase">
            Currently In
          </div>
          <div class="fs-4 fw-bold text-dark">{{ number_format($stats['active']) }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 mb-3">
    <div class="card flex-fill border-0 shadow-sm">
      <div class="card-body py-3 d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10"
             style="width:46px;height:46px;flex-shrink:0">
          <i class="fas fa-sign-out-alt text-info fs-5"></i>
        </div>
        <div>
          <div class="text-muted" style="font-size:12px;font-weight:600;letter-spacing:.05em;text-transform:uppercase">
            Completed
          </div>
          <div class="fs-4 fw-bold text-dark">{{ number_format($stats['completed']) }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3 mb-3">
    <div class="card flex-fill border-0 shadow-sm">
      <div class="card-body py-3 d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10"
             style="width:46px;height:46px;flex-shrink:0">
          <i class="fas fa-calendar-day text-warning fs-5"></i>
        </div>
        <div>
          <div class="text-muted" style="font-size:12px;font-weight:600;letter-spacing:.05em;text-transform:uppercase">
            Today
          </div>
          <div class="fs-4 fw-bold text-dark">{{ number_format($stats['today']) }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── Filter Form ── --}}
<div class="card mb-4 shadow-sm">
  <div class="card-body py-3">
    <form method="GET" action="{{ route($routePrefix . 'attendance-list.index') }}" class="row g-2 align-items-end">
      {{-- Search --}}
      <div class="col-md-3">
        <label class="form-label mb-1" style="font-size:12px;font-weight:600">Search</label>
        <div class="input-group input-group-sm">
          <span class="input-group-text"><i class="fas fa-search"></i></span>
          <input type="text" name="search" class="form-control"
                 placeholder="Name or email…" value="{{ request('search') }}"/>
        </div>
      </div>
      {{-- User filter --}}
      <div class="col-md-2">
        <label class="form-label mb-1" style="font-size:12px;font-weight:600">User</label>
        <select name="user_id" class="form-select form-select-sm">
          <option value="">All Users</option>
          @foreach($users as $u)
            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
              {{ $u->name }}
            </option>
          @endforeach
        </select>
      </div>
      {{-- Status filter --}}
      <div class="col-md-2">
        <label class="form-label mb-1" style="font-size:12px;font-weight:600">Status</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">All Status</option>
          <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
          <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
        </select>
      </div>
      {{-- Date From --}}
      <div class="col-md-2">
        <label class="form-label mb-1" style="font-size:12px;font-weight:600">Date From</label>
        <input type="date" name="date_from" class="form-control form-control-sm"
               value="{{ request('date_from') }}"/>
      </div>
      {{-- Date To --}}
      <div class="col-md-2">
        <label class="form-label mb-1" style="font-size:12px;font-weight:600">Date To</label>
        <input type="date" name="date_to" class="form-control form-control-sm"
               value="{{ request('date_to') }}"/>
      </div>
      {{-- Buttons --}}
      <div class="col-md-1 d-flex gap-1">
        <button type="submit" class="btn btn-primary btn-sm px-3">
          <i class="fas fa-filter"></i>
        </button>
        @if(request()->hasAny(['search','user_id','status','date_from','date_to']))
          <a href="{{ route($routePrefix . 'attendance-list.index') }}" class="btn btn-outline-secondary btn-sm px-2">
            <i class="fas fa-times"></i>
          </a>
        @endif
      </div>
    </form>
  </div>
</div>

{{-- ── Success / Error Alerts ── --}}
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- ── Main Table ── --}}
<section class="row">
  <div class="col-12">
    <div class="card flex-fill shadow-sm">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">
          {{ __('Attendance DataTable') }}
          <span class="badge bg-secondary ms-2">{{ $logs->total() }}</span>
        </h5>
        <small class="text-muted">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}</small>
      </div>

      <div class="table-responsive">
        <table class="table table-hover my-0 data-table">
          <thead>
            <tr>
              <th class="d-none d-xl-table-cell">{{ __('SL') }}</th>
              <th>{{ __('Name') }}</th>
              <th class="d-none d-xl-table-cell">{{ __('Email') }}</th>
              <th class="d-none d-xl-table-cell">{{ __('Check In') }}</th>
              <th class="d-none d-xl-table-cell">{{ __('Status of Checkin') }}</th>
              <th class="d-none d-xl-table-cell">{{ __('Check Out') }}</th>
              <th class="d-none d-xl-table-cell">{{ __('Status of Checkout') }}</th>
              <th class="d-none d-xl-table-cell">{{ __('Duration') }}</th>
              <th>{{ __('Status') }}</th>
              <th class="d-none d-xl-table-cell">{{ __('Device') }}</th>
              <th>{{ __('Action') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($logs as $k => $log)
              <tr>
                {{-- SL --}}
                <td class="d-none d-xl-table-cell text-muted">
                  {{ $logs->firstItem() + $k }}
                </td>

                {{-- Name --}}
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10"
                         style="width:34px;height:34px;flex-shrink:0;font-size:13px;font-weight:700;color:#6366f1">
                      {{ strtoupper(substr($log->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                      <strong>{{ $log->name ?? '—' }}</strong>
                      <div class="text-muted d-xl-none" style="font-size:11px">{{ $log->email ?? '' }}</div>
                    </div>
                  </div>
                </td>

                {{-- Email --}}
                <td class="d-none d-xl-table-cell">
                  <span class="text-muted">{{ $log->email ?? '—' }}</span>
                </td>

                {{-- Check In --}}
                <td class="d-none d-xl-table-cell">
                  @if($log->checkin_at)
                    <div style="font-size:13px;font-weight:500">
                      {{ $log->checkin_at->format('d M Y') }}
                    </div>
                    <div class="text-muted" style="font-size:12px">
                      <i class="fas fa-clock me-1"></i>{{ $log->checkin_at->format('h:i A') }}
                    </div>
                    @if($log->checkin_address)
                      <div class="text-muted" style="font-size:11px;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                           title="{{ $log->checkin_address }}">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($log->checkin_address, 30) }}
                      </div>
                    @endif
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>

                <td>
                 
                    <span class="badge bg-secondary">
                      <i class="fas fa-check me-1"></i>{{ $log->checkin_status }}
                    </span>
                  
                </td>

                {{-- Check Out --}}
                <td class="d-none d-xl-table-cell">
                  @if($log->checkout_at)
                    <div style="font-size:13px;font-weight:500">
                      {{ $log->checkout_at->format('d M Y') }}
                    </div>
                    <div class="text-muted" style="font-size:12px">
                      <i class="fas fa-clock me-1"></i>{{ $log->checkout_at->format('h:i A') }}
                    </div>
                    @if($log->checkout_address)
                      <div class="text-muted" style="font-size:11px;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                           title="{{ $log->checkout_address }}">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($log->checkout_address, 30) }}
                      </div>
                    @endif
                  @else
                    <span class="badge bg-warning text-dark" style="font-size:11px">Not checked out</span>
                  @endif
                </td>

                <td>
                 
                    <span class="badge bg-secondary">
                      <i class="fas fa-check me-1"></i>{{ $log->checkout_status }}
                    </span>
                  
                </td>

                {{-- Duration --}}
                <td class="d-none d-xl-table-cell">
                  @if($log->session_duration)
                    <span class="badge bg-info bg-opacity-75 text-dark">
                      <i class="fas fa-hourglass-half me-1"></i>
                      {{ $log->formatted_duration }}
                    </span>
                  @elseif($log->status === 'active')
                    <span class="text-success" style="font-size:12px">
                      <i class="fas fa-circle me-1" style="font-size:8px"></i>In progress
                    </span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>

                {{-- Status --}}
                <td>
                  @if($log->status === 'active')
                    <span class="badge bg-success">
                      <i class="fas fa-circle me-1" style="font-size:8px"></i>Active
                    </span>
                  @else
                    <span class="badge bg-secondary">
                      <i class="fas fa-check me-1"></i>Completed
                    </span>
                  @endif
                </td>

                {{-- Device --}}
                <td class="d-none d-xl-table-cell">
                  <div style="font-size:12px">
                    @if($log->device_type)
                      <span class="text-muted">
                        <i class="fas fa-{{ $log->device_type === 'Mobile' ? 'mobile-alt' : ($log->device_type === 'Tablet' ? 'tablet-alt' : 'desktop') }} me-1"></i>
                        {{ $log->device_type }}
                      </span>
                    @endif
                    @if($log->browser)
                      <div class="text-muted">
                        <i class="fas fa-globe me-1"></i>{{ $log->browser }}
                      </div>
                    @endif
                    @if($log->ip_address)
                      <div class="text-muted" style="font-size:11px">
                        <i class="fas fa-wifi me-1"></i>{{ $log->ip_address }}
                      </div>
                    @endif
                  </div>
                </td>

                {{-- Actions --}}
                <td>
                  <div class="d-flex gap-1">
                    {{-- View detail --}}
                    <a href="{{ route($routePrefix . 'attendance-list.show', $log->id) }}"
                       class="btn btn-outline-info btn-sm" title="View Details">
                      <i class="fas fa-eye"></i>
                    </a>

                    {{-- Force checkout (only for active) --}}
                    @if($log->status === 'active')
                      <form action="{{ route($routePrefix . 'attendance-list.force-checkout', $log->id) }}"
                            method="POST" style="display:inline">
                        @csrf
                        @method('PATCH')
                        <button type="button" class="btn btn-outline-warning btn-sm"
                                title="Force Checkout"
                                onclick="del(event, this)"
                                data-message="Force checkout this user?">
                          <i class="fas fa-sign-out-alt"></i>
                        </button>
                      </form>
                    @endif

                    {{-- Delete --}}
                    <form action="{{ route($routePrefix . 'attendance-list.destroy', $log->id) }}"
                          method="POST" style="display:inline">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="btn btn-outline-danger btn-sm"
                              title="Delete" onclick="del(event, this)">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center py-5">
                  <div class="text-muted">
                    <i class="fas fa-clipboard-list fa-3x mb-3 d-block opacity-25"></i>
                    {{ __('No attendance logs found') }}
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @if($logs->hasPages())
        <div class="card-footer bg-white border-top">
          {{ $logs->links() }}
        </div>
      @endif

    </div>
  </div>
</section>

@endsection

@section('script')
{{-- No extra script needed — uses existing del() from your layout --}}
@endsection

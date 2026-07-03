@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp

@section('title')
    {{ __('Attendance Detail') }}
@endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route($routePrefix . 'attendance-list.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h1 class="h3 mb-0">{{ __('Attendance Detail') }}</h1>
    </div>
    @if($attendance_log->status === 'active')
      <form action="{{ route('attendance-list.force-checkout', $attendance_log->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-warning btn-sm">
          <i class="fas fa-sign-out-alt me-1"></i>Force Checkout
        </button>
      </form>
    @endif
  </div>
@endsection

@section('content')

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="row g-4">

  {{-- ── Left: User + Status ── --}}
  <div class="col-lg-4">

    {{-- User Card --}}
    <div class="card shadow-sm mb-4">
      <div class="card-body text-center py-4">
        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 mx-auto mb-3"
             style="width:64px;height:64px;font-size:24px;font-weight:700;color:#6366f1">
          {{ strtoupper(substr($attendance_log->name ?? '?', 0, 1)) }}
        </div>
        <h5 class="fw-bold mb-1">{{ $attendance_log->name ?? '—' }}</h5>
        <p class="text-muted mb-3" style="font-size:13px">{{ $attendance_log->email ?? '—' }}</p>
        @if($attendance_log->status === 'active')
          <span class="badge bg-success px-3 py-2 fs-6">
            <i class="fas fa-circle me-1" style="font-size:9px"></i>Currently Active
          </span>
        @else
          <span class="badge bg-secondary px-3 py-2 fs-6">
            <i class="fas fa-check me-1"></i>Session Completed
          </span>
        @endif
      </div>
    </div>

    {{-- Session Summary --}}
    <div class="card shadow-sm mb-4">
      <div class="card-header">
        <h6 class="card-title mb-0"><i class="fas fa-clock me-2 text-primary"></i>Session Summary</h6>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <tbody>
            <tr>
              <td class="text-muted ps-3" style="font-size:13px;width:40%">Duration</td>
              <td class="fw-bold" style="font-size:13px">
                @if($attendance_log->session_duration)
                  <span class="badge bg-info bg-opacity-75 text-dark">
                    <i class="fas fa-hourglass-half me-1"></i>
                    {{ $attendance_log->formatted_duration }}
                  </span>
                @elseif($attendance_log->status === 'active')
                  <span class="text-success" style="font-size:12px">In progress…</span>
                @else
                  —
                @endif
              </td>
            </tr>
            <tr>
              <td class="text-muted ps-3" style="font-size:13px">Log ID</td>
              <td class="fw-bold" style="font-size:13px">#{{ $attendance_log->id }}</td>
            </tr>
            <tr>
              <td class="text-muted ps-3" style="font-size:13px">User ID</td>
              <td style="font-size:13px">{{ $attendance_log->user_id }}</td>
            </tr>
            <tr>
              <td class="text-muted ps-3" style="font-size:13px">Recorded</td>
              <td style="font-size:12px" class="text-muted">{{ $attendance_log->created_at->format('d M Y H:i') }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    {{-- Device Info --}}
    <div class="card shadow-sm">
      <div class="card-header">
        <h6 class="card-title mb-0"><i class="fas fa-laptop me-2 text-primary"></i>Device Info</h6>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <tbody>
            <tr>
              <td class="text-muted ps-3" style="font-size:13px;width:40%">Device</td>
              <td style="font-size:13px">
                @if($attendance_log->device_type)
                  <i class="fas fa-{{ $attendance_log->device_type === 'Mobile' ? 'mobile-alt' : ($attendance_log->device_type === 'Tablet' ? 'tablet-alt' : 'desktop') }} me-1 text-muted"></i>
                  {{ $attendance_log->device_type }}
                @else —
                @endif
              </td>
            </tr>
            <tr>
              <td class="text-muted ps-3" style="font-size:13px">Browser</td>
              <td style="font-size:13px">{{ $attendance_log->browser ?? '—' }}</td>
            </tr>
            <tr>
              <td class="text-muted ps-3" style="font-size:13px">Platform</td>
              <td style="font-size:13px">{{ $attendance_log->platform ?? '—' }}</td>
            </tr>
            <tr>
              <td class="text-muted ps-3" style="font-size:13px">IP Address</td>
              <td style="font-size:13px">
                <code>{{ $attendance_log->ip_address ?? '—' }}</code>
              </td>
            </tr>
            @if($attendance_log->user_agent)
            <tr>
              <td class="text-muted ps-3" style="font-size:13px">User Agent</td>
              <td style="font-size:11px;color:#64748b;word-break:break-all">
                {{ Str::limit($attendance_log->user_agent, 80) }}
              </td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

  </div>

  {{-- ── Right: Check In / Check Out ── --}}
  <div class="col-lg-8">

    {{-- Check In --}}
    <div class="card shadow-sm mb-4 border-start border-success border-3">
      <div class="card-header d-flex align-items-center gap-2">
        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success"
             style="width:30px;height:30px;flex-shrink:0">
          <i class="fas fa-sign-in-alt text-white" style="font-size:12px"></i>
        </div>
        <h6 class="card-title mb-0">Check In</h6>
        @if($attendance_log->checkin_at)
          <span class="ms-auto text-muted" style="font-size:13px">
            {{ $attendance_log->checkin_at->format('D, d M Y · h:i:s A') }}
          </span>
        @endif
      </div>
      <div class="card-body">
        @if($attendance_log->checkin_at)
          <div class="row g-3">
            {{-- Map placeholder --}}
            @if($attendance_log->checkin_latitude && $attendance_log->checkin_longitude)
              <div class="col-12">
                <div class="rounded overflow-hidden border" style="height:180px;background:#f1f5f9;position:relative">
                  <iframe
                    src="https://maps.google.com/maps?q={{ $attendance_log->checkin_latitude }},{{ $attendance_log->checkin_longitude }}&z=15&output=embed"
                    width="100%" height="180" frameborder="0" style="border:0" allowfullscreen
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                  </iframe>
                </div>
              </div>
            @endif
            <div class="col-sm-6">
              <div class="p-3 rounded bg-light">
                <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;font-weight:600">
                  <i class="fas fa-map-marker-alt me-1 text-danger"></i>Address
                </div>
                <div style="font-size:13px">{{ $attendance_log->checkin_address ?? '—' }}</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 rounded bg-light">
                <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;font-weight:600">
                  <i class="fas fa-crosshairs me-1 text-primary"></i>Coordinates
                </div>
                <div style="font-size:13px">
                  <div>Lat: <code>{{ $attendance_log->checkin_latitude }}</code></div>
                  <div>Lng: <code>{{ $attendance_log->checkin_longitude }}</code></div>
                  @if($attendance_log->checkin_accuracy)
                    <div class="text-muted" style="font-size:12px">
                      Accuracy: {{ round($attendance_log->checkin_accuracy) }}m
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @else
          <div class="text-center text-muted py-4">
            <i class="fas fa-map-marker-alt fa-2x mb-2 d-block opacity-25"></i>
            No check-in data
          </div>
        @endif
      </div>
    </div>

    {{-- Check Out --}}
    <div class="card shadow-sm border-start border-{{ $attendance_log->checkout_at ? 'secondary' : 'warning' }} border-3">
      <div class="card-header d-flex align-items-center gap-2">
        <div class="rounded-circle d-flex align-items-center justify-content-center bg-{{ $attendance_log->checkout_at ? 'secondary' : 'warning' }}"
             style="width:30px;height:30px;flex-shrink:0">
          <i class="fas fa-sign-out-alt text-white" style="font-size:12px"></i>
        </div>
        <h6 class="card-title mb-0">Check Out</h6>
        @if($attendance_log->checkout_at)
          <span class="ms-auto text-muted" style="font-size:13px">
            {{ $attendance_log->checkout_at->format('D, d M Y · h:i:s A') }}
          </span>
        @else
          <span class="ms-auto badge bg-warning text-dark">Not checked out yet</span>
        @endif
      </div>
      <div class="card-body">
        @if($attendance_log->checkout_at)
          <div class="row g-3">
            @if($attendance_log->checkout_latitude && $attendance_log->checkout_longitude)
              <div class="col-12">
                <div class="rounded overflow-hidden border" style="height:180px">
                  <iframe
                    src="https://maps.google.com/maps?q={{ $attendance_log->checkout_latitude }},{{ $attendance_log->checkout_longitude }}&z=15&output=embed"
                    width="100%" height="180" frameborder="0" style="border:0" allowfullscreen
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                  </iframe>
                </div>
              </div>
            @endif
            <div class="col-sm-6">
              <div class="p-3 rounded bg-light">
                <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;font-weight:600">
                  <i class="fas fa-map-marker-alt me-1 text-danger"></i>Address
                </div>
                <div style="font-size:13px">{{ $attendance_log->checkout_address ?? '—' }}</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 rounded bg-light">
                <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;font-weight:600">
                  <i class="fas fa-crosshairs me-1 text-primary"></i>Coordinates
                </div>
                <div style="font-size:13px">
                  <div>Lat: <code>{{ $attendance_log->checkout_latitude }}</code></div>
                  <div>Lng: <code>{{ $attendance_log->checkout_longitude }}</code></div>
                  @if($attendance_log->checkout_accuracy)
                    <div class="text-muted" style="font-size:12px">
                      Accuracy: {{ round($attendance_log->checkout_accuracy) }}m
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @else
          <div class="text-center py-4">
            <i class="fas fa-hourglass-half fa-2x mb-2 d-block text-warning opacity-50"></i>
            <p class="text-muted mb-3">This session is still active.</p>
            <form action="{{ route('attendance-list.force-checkout', $attendance_log->id) }}" method="POST">
              @csrf
              @method('PATCH')
              <button type="submit" class="btn btn-warning btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i>Force Checkout Now
              </button>
            </form>
          </div>
        @endif
      </div>
    </div>

  </div>
</div>

{{-- Delete button at bottom --}}
<div class="d-flex justify-content-end mt-4">
  <form action="{{ route('attendance-list.destroy', $attendance_log->id) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="button" class="btn btn-outline-danger btn-sm" onclick="del(event, this)">
      <i class="fas fa-trash-alt me-1"></i>Delete This Log
    </button>
  </form>
</div>

@endsection

@section('script')
@endsection

@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp

@section('title')
    {{ __('Manage Notifications') }}
@endsection

@section('header')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3">{{ __('App Notifications') }}</h1>
    <a href="{{ route("{$routePrefix}user-notifications.create") }}" class="btn btn-primary">
      <i class="fas fa-plus"></i>
      <span class="ps-1">{{ __('Create Notification') }}</span>
    </a>
  </div>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0"><h5 class="card-title">Total</h5></div>
                    <div class="col-auto"><div class="stat text-primary"><i class="fas fa-bell"></i></div></div>
                </div>
                <h1 class="mt-1 mb-3">{{ $stats['total'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0"><h5 class="card-title">Sent</h5></div>
                    <div class="col-auto"><div class="stat text-success"><i class="fas fa-paper-plane"></i></div></div>
                </div>
                <h1 class="mt-1 mb-3">{{ $stats['sent'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0"><h5 class="card-title">Scheduled</h5></div>
                    <div class="col-auto"><div class="stat text-warning"><i class="fas fa-clock"></i></div></div>
                </div>
                <h1 class="mt-1 mb-3">{{ $stats['scheduled'] }}</h1>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col mt-0"><h5 class="card-title">Drafts</h5></div>
                    <div class="col-auto"><div class="stat text-muted"><i class="fas fa-file-alt"></i></div></div>
                </div>
                <h1 class="mt-1 mb-3">{{ $stats['draft'] }}</h1>
            </div>
        </div>
    </div>
</div>

<section class="row">
    <div class="col-12">
        <div class="card flex-fill">
            <div class="card-header bg-white border-bottom py-3">
                <form action="{{ route("{$routePrefix}user-notifications.index") }}" method="GET" class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search title...">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">-- All Statuses --</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">-- All Categories --</option>
                            @foreach($categories as $key => $value)
                                <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-secondary"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover my-0 align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category / Type</th>
                            <th>Target Group</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Dates</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-{{ \App\Models\UserAppNotificastion::iconColorMap()[$notification->type] ?? 'primary' }} me-2">
                                            <i class="{{ $notification->icon_class }}"></i>
                                        </span>
                                        <strong>{{ $notification->title }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $notification->category }}</span><br>
                                    <small class="text-muted">{{ $notification->type }}</small>
                                </td>
                                <td>
                                    @if($notification->is_broadcast)
                                        <span class="badge bg-primary">Broadcast (All)</span>
                                    @elseif(!empty($notification->target_employee_ids))
                                        <span class="badge bg-info">Selected Employees ({{ count($notification->target_employee_ids) }})</span>
                                    @elseif(!empty($notification->target_roles))
                                        <span class="badge bg-dark">Roles: {{ implode(', ', $notification->target_roles) }}</span>
                                    @else
                                        <span class="badge bg-secondary">System Default</span>
                                    @endif
                                </td>
                                <td>
                                    @if($notification->status === 'sent')
                                        <span class="badge bg-success">Sent</span>
                                    @elseif($notification->status === 'scheduled')
                                        <span class="badge bg-warning text-dark">Scheduled</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $notification->creator->name ?? 'System' }}</td>
                                <td>
                                    @if($notification->status === 'scheduled' && $notification->scheduled_at)
                                        <small class="text-warning">⏰ {{ $notification->scheduled_at->format('M d, Y H:i') }}</small>
                                    @elseif($notification->status === 'sent' && $notification->sent_at)
                                        <small class="text-success">🚀 {{ $notification->sent_at->format('M d, Y H:i') }}</small>
                                    @else
                                        <small class="text-muted">✍️ {{ $notification->created_at->format('M d, Y') }}</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route("{$routePrefix}user-notifications.show", $notification) }}" class="btn btn-sm btn-light border" title="View details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($notification->status !== 'sent')
                                            <a href="{{ route("{$routePrefix}user-notifications.edit", $notification) }}" class="btn btn-sm btn-light border text-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route("{$routePrefix}user-notifications.sendNow", $notification) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-light border text-success" title="Send Now" onclick="return confirm('Send this notification immediately?')">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route("{$routePrefix}user-notifications.duplicate", $notification) }}" class="btn btn-sm btn-light border text-info" title="Duplicate">
                                            <i class="fas fa-copy"></i>
                                        </a>
                                        <form action="{{ route("{$routePrefix}user-notifications.destroy", $notification) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No notifications found matching parameters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($notifications->hasPages())
                <div class="card-footer bg-white border-top">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
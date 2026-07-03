@extends('layouts.admin')

@section('title')
    {{ __('Manage Leave') }}
@endsection

@section('header')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-3">Manage Leaves</h1>

        @php
            $role = Auth::user()->role->slug ?? '';
        @endphp

        {{-- Only roles that can apply leave see the Add New button --}}
        @if (in_array($role, ['super-admin', 'hr', 'employee']))
            <a href="{{ match ($role) {
                'super-admin' => route('leaves.create'),
                'hr' => route('hr.leaves.create'),
                default => route('employee.leaves.create'),
            } }}"
                class="btn btn-primary">
                <i class="fas fa-plus"></i>
                <span class="ps-1">{{ __('Add New') }} {{ Auth::user()->employee->id }} </span>
            </a>
        @endif
    </div>
@endsection

@section('content')

    @php
        $role = Auth::user()->role->slug ?? '';
        $loggedInEmployeeId = Auth::user()->employee->id ?? null;
    @endphp

    <section class="row">
        <div class="col-12">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card flex-fill">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th scope="col">SL</th>
                            <th scope="col">Employee</th>
                            <th scope="col">Leave Type</th>
                            <th scope="col">Date Applied</th>
                            <th scope="col">Leave From</th>
                            <th scope="col">Leave To</th>
                            <th scope="col">Current Approver</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leaves as $leave)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>

                                {{-- Employee Name --}}
                                <td>{{ $leave->employee->firstname }} {{ $leave->employee->lastname }}</td>

                                {{-- Leave Type --}}
                                <td>
                                    <span class="badge bg-info">
                                        {{ $leave->leaveTypeRel?->name ?? (\App\Models\LeaveType::find($leave->leave_type)?->name ?? 'Unknown') }}
                                    </span>
                                </td>

                                {{-- Date Applied --}}
                                <td>{{ $leave->created_at->format('d M Y') }}</td>

                                {{-- Leave From --}}
                                <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>

                                {{-- Leave To --}}
                                <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>

                                {{-- Current Approver --}}
                                <td>
                                    @if ($leave->currentApprover)
                                        {{ $leave->currentApprover->firstname }} {{ $leave->currentApprover->lastname }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- Status Badge --}}
                                <td>
                                    @switch($leave->status)
                                        @case(0)
                                            <span class="badge bg-danger">Rejected</span>
                                        @break

                                        @case(1)
                                            <span class="badge bg-warning text-dark">Approved</span>
                                        @break

                                        @case(2)
                                            <span class="badge bg-info">Pending</span>
                                        @break

                                        @case(3)
                                            <span class="badge bg-primary">Approved by Manager</span>
                                        @break

                                        @case(4)
                                            <span class="badge bg-success">Fully Approved</span>
                                        @break

                                        @default
                                            <span class="badge bg-secondary">Unknown</span>
                                    @endswitch
                                </td>

                                {{-- Actions Container --}}
                                <td>
                                    <div class="d-flex justify-content-center align-items-center gap-2">

                                        @if (in_array($role, ['super-admin', 'administrator']))
                                            {{-- Approve / Reject Action Cluster --}}
                                            @if (($loggedInEmployeeId && $leave->current_approver_id == $loggedInEmployeeId) || !in_array($leave->status, [1, 4]))
                                                {{-- Approve --}}
                                                <form
                                                    action="{{ match ($role) {
                                                        'super-admin' => route('leaves.approve', $leave->id),
                                                        'hr' => route('hr.leaves.approve', $leave->id),
                                                        'team-lead' => route('team-lead.leaves.approve', $leave->id),
                                                        'manager' => route('manager.leaves.approve', $leave->id),
                                                        default => route('leaves.approve', $leave->id),
                                                    } }}"
                                                    method="POST" class="d-inline m-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="return confirm('Approve this leave request?')">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>

                                                {{-- Reject --}}
                                                <form
                                                    action="{{ match ($role) {
                                                        'super-admin' => route('leaves.reject', $leave->id),
                                                        'hr' => route('hr.leaves.reject', $leave->id),
                                                        'team-lead' => route('team-lead.leaves.reject', $leave->id),
                                                        'manager' => route('manager.leaves.reject', $leave->id),
                                                        default => route('leaves.reject', $leave->id),
                                                    } }}"
                                                    method="POST" class="d-inline m-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Reject this leave request?')">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </form>

                                                {{-- History Trail Trigger Button --}}
                                                <button type="button" class="btn btn-outline-info btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#historyModal{{ $leave->id }}">
                                                    <i class="fas fa-history"></i>
                                                </button>

                                                {{-- Workflow History Trail Modal --}}
                                                <div class="modal fade text-left" id="historyModal{{ $leave->id }}"
                                                    tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Leave History —
                                                                    {{ $leave->employee->firstname }}</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                @forelse($leave->histories as $history)
                                                                    <div class="d-flex align-items-start mb-3">
                                                                        <div class="me-3 mt-1">
                                                                            @if ($history->action === 'approved')
                                                                                <span class="badge bg-success"><i
                                                                                        class="fas fa-check"></i></span>
                                                                            @elseif($history->action === 'rejected')
                                                                                <span class="badge bg-danger"><i
                                                                                        class="fas fa-times"></i></span>
                                                                            @elseif($history->action === 'forwarded')
                                                                                <span class="badge bg-primary"><i
                                                                                        class="fas fa-arrow-right"></i></span>
                                                                            @else
                                                                                <span class="badge bg-warning text-dark"><i
                                                                                        class="fas fa-clock"></i></span>
                                                                            @endif
                                                                        </div>
                                                                        <div>
                                                                            <div class="fw-semibold">{{ $history->note }}
                                                                            </div>
                                                                            <small
                                                                                class="text-muted">{{ $history->created_at->format('d M Y, h:i A') }}</small>
                                                                        </div>
                                                                    </div>
                                                                @empty
                                                                    <p class="text-muted text-center">No history yet.</p>
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif

                                        {{-- Edit Action --}}
                                        @if (in_array($role, ['super-admin', 'hr', 'administrator']))
                                            <a href="{{ match ($role) {
                                                'super-admin' => route('leaves.edit', $leave->id),
                                                'administrator' => route('admin.leaves.edit', $leave->id),
                                                'hr' => route('hr.leaves.edit', $leave->id),
                                                default => route('leaves.edit', $leave->id),
                                            } }}"
                                                class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        {{-- Delete Action (Fixed Typo Here) --}}
                                        @if (in_array($role, ['super-admin', 'administrator']))
                                            <form
                                                action="{{ match ($role) {
                                                    'super-admin' => route('leaves.destroy', $leave->id),
                                                    'administrator' => route('admin.leaves.destroy', $leave->id),
                                                    default => route('leaves.destroy', $leave->id),
                                                } }}"
                                                method="POST" class="d-inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Delete this leave record?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Locked State Fallback --}}
                                        @if (
                                            !($loggedInEmployeeId && $leave->current_approver_id == $loggedInEmployeeId && !in_array($leave->status, [1, 4])) &&
                                                !in_array($role, ['super-admin', 'hr', 'administrator']))
                                            <span class="text-muted">—</span>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">{{ __('No Data Found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('script')
@endsection

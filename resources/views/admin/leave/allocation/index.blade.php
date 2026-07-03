@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f8fafc; min-height: 100vh;">
    
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between pb-3 mb-4 border-bottom" style="border-color: #e2e8f0 !important;">
        <div class="mb-3 mb-md-0">
            <h1 class="h3 font-weight-bold text-dark mb-1" style="letter-spacing: -0.5px; font-weight: 800;">Leave Allocations</h1>
            <p class="text-muted small mb-0" style="font-size: 0.85rem;">Configure annual quota policies for company leave accounts</p>
        </div>
        
        <div class="bg-white p-2 rounded border shadow-sm" style="border-radius: 8px; border-color: #e2e8f0 !important;">
            <form action="{{ route($routePrefix . 'leave-allocation.index') }}" method="GET" id="yearPickerForm" class="form-inline m-0">
                <i class="far fa-calendar-alt text-primary mr-2 ml-1" style="font-size: 0.9rem;"></i>
                <label for="year" class="mr-2 font-weight-bold text-secondary text-uppercase tracking-wider small" style="font-size: 0.7rem; letter-spacing: 0.5px;">Calendar Year:</label>
                <select name="year" id="year" class="form-control form-control-sm border-0 bg-light text-primary font-weight-bold px-3" style="font-size: 0.85rem; height: 32px; border-radius: 6px; cursor: pointer;" onchange="document.getElementById('yearPickerForm').submit();">
                    @for($y = date('Y') - 2; $y <= date('Y') + 3; $y++)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }} Active Policy</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm py-3 px-4 mb-4 d-flex align-items-center" style="background-color: #ecfdf5; color: #065f46; border-left: 4px solid #10b981 !important; border-radius: 6px;">
            <i class="fas fa-check-circle mr-3" style="font-size: 1.1rem;"></i>
            <span class="font-weight-medium small">{{ session('success') }}</span>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm rounded-lg" style="border-radius: 10px; border: 1px solid #e2e8f0 !important; background: #fff; overflow: hidden;">
                <div class="card-header bg-white border-bottom py-3 px-4" style="border-color: #f1f5f9 !important;">
                    <h5 class="font-weight-bold text-dark m-0" style="font-size: 0.95rem; font-weight: 700;">Active Rules for Year: <span class="text-primary">{{ $selectedYear }}</span></h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 custom-premium-table">
                            <thead class="text-uppercase font-weight-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.8px; background-color: #f8fafc;">
                                <tr>
                                    <th class="px-4 py-3" style="border-bottom: 1px solid #e2e8f0;">Leave Type</th>
                                    <th class="py-3" style="border-bottom: 1px solid #e2e8f0; width: 20%;">Code</th>
                                    <th class="py-3 text-center" style="border-bottom: 1px solid #e2e8f0; width: 25%;">Allocated Quota</th>
                                    <th class="px-4 py-3 text-center" style="border-bottom: 1px solid #e2e8f0; width: 20%;">Action</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.85rem;">
                                @forelse($allocations as $allocation)
                                    <tr class="row-hover-transition">
                                        <td class="px-4 py-3 align-middle">
                                            <span class="font-weight-bold text-dark" style="font-size: 0.88rem;">{{ $allocation->leaveType->name }}</span>
                                        </td>
                                        <td class="py-3 align-middle">
                                            <span class="badge px-2.5 py-1.5 font-weight-bold border text-uppercase" style="border-radius: 6px; background-color: #f1f5f9; color: #475569; border-color: #e2e8f0 !important; font-size: 0.72rem;">
                                                {{ $allocation->leaveType->code }}
                                            </span>
                                        </td>
                                        <td class="py-3 align-middle text-center">
                                            <span class="font-weight-black h6 mb-0 text-success font-weight-bold" style="font-weight: 800; font-size: 0.95rem;">
                                                {{ $allocation->days }} <span class="small font-weight-normal text-muted" style="font-size: 0.75rem;">Days</span>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 align-middle text-center">
                                            <form action="{{ route($routePrefix . 'leave-allocation.destroy', $allocation->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to drop this allocation rule?');" class="m-0">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger border rounded px-3 py-1" style="font-size: 0.78rem; background: #fff; transition: all 0.2s;">
                                                    <i class="fas fa-trash-alt mr-1 small"></i> Reset
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted bg-white">
                                            <div class="py-4">
                                                <i class="fas fa-calendar-times fa-3x mb-3 text-muted" style="opacity: 0.35;"></i>
                                                <h6 class="font-weight-bold text-secondary mb-1" style="font-size: 0.9rem;">No Rules Allocated</h6>
                                                <p class="small text-muted mb-0 mx-auto" style="max-width: 320px; font-size: 0.78rem;">No leave counts configured for the year {{ $selectedYear }}.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; border: 1px solid #e2e8f0 !important; background: #fff;">
                <div class="card-header py-3 px-4 border-bottom d-flex align-items-center bg-white" style="border-color: #f1f5f9 !important;">
                    <i class="fas fa-plus-circle text-primary mr-2" style="font-size: 0.9rem;"></i>
                    <h6 class="font-weight-bold m-0 text-dark" style="font-size: 0.88rem; font-weight: 700;">Allocate Quota Days</h6>
                </div>
                <div class="card-body px-4 py-4">
                    <form action="{{ route($routePrefix . 'leave-allocation.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="year" value="{{ $selectedYear }}">
                        
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted text-uppercase tracking-wider mb-2 d-block" style="font-size: 0.68rem; letter-spacing: 0.5px;">Leave Type Category</label>
                            <select name="leave_type_id" class="form-control input-element-custom" required style="border-radius: 6px; height: 40px; font-size: 0.85rem; border-color: #cbd5e1;">
                                <option value="" disabled selected>-- Choose Type --</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted text-uppercase tracking-wider mb-2 d-block" style="font-size: 0.68rem; letter-spacing: 0.5px;">Number of Days Allocation</label>
                            <div class="input-group">
                                <input type="number" name="days" class="form-control input-element-custom" placeholder="e.g. 6" min="0" required style="border-radius: 6px 0 0 6px; height: 40px; font-size: 0.85rem; border-color: #cbd5e1;">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-light text-muted small px-3 border-left-0" style="border-radius: 0 6px 6px 0; font-size: 0.8rem; border-color: #cbd5e1;">Days</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm" style="background-color: #0066ff; border: none; height: 40px; font-size: 0.85rem; border-radius: 6px;">
                            Save Allocation for {{ $selectedYear }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 10px; border: 1px solid #e2e8f0 !important; background: #fff;">
                <div class="card-header py-3 px-4 border-bottom d-flex align-items-center bg-white" style="border-color: #f1f5f9 !important;">
                    <i class="fas fa-cog text-secondary mr-2" style="font-size: 0.9rem;"></i>
                    <h6 class="font-weight-bold m-0 text-dark" style="font-size: 0.88rem; font-weight: 700;">Create New Leave Type Base</h6>
                </div>
                <div class="card-body px-4 py-4" style="background-color: #fafafa; border-radius: 0 0 10px 10px;">
                    <form action="{{ route($routePrefix . 'leave-types.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-7 form-group mb-0 pr-1">
                                <label class="small font-weight-bold text-muted text-uppercase tracking-wider mb-1.5 d-block" style="font-size: 0.65rem;">Category Title</label>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="Medical Leave" required style="border-radius: 6px; height: 36px; font-size: 0.82rem; border-color: #cbd5e1;">
                            </div>
                            <div class="col-5 form-group mb-0 pl-1">
                                <label class="small font-weight-bold text-muted text-uppercase tracking-wider mb-1.5 d-block" style="font-size: 0.65rem;">Short Code</label>
                                <input type="text" name="code" class="form-control form-control-sm text-uppercase" placeholder="ML" required style="border-radius: 6px; height: 36px; font-size: 0.82rem; border-color: #cbd5e1; font-weight: 700;">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-dark btn-block font-weight-bold mt-3" style="border-radius: 6px; height: 36px; font-size: 0.8rem; border-color: #475569; color: #475569; background: #fff; transition: all 0.2s;">
                            Add Type Category Base
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .row-hover-transition {
        transition: background-color 0.15s ease-in-out;
    }
    .row-hover-transition:hover {
        background-color: #f8fafc !important;
    }
    .custom-premium-table th, .custom-premium-table td {
        vertical-align: middle !important;
        border-top: none !important;
        border-bottom: 1px solid #f1f5f9;
    }
    .input-element-custom:focus {
        border-color: #0066ff !important;
        box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1) !important;
        outline: none;
    }
</style>
@endsection
@extends('layouts.admin')

@section('title')
    {{ __('Asset Management') }}
@endsection

@section('header')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-3">Asset Management</h1>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('employee-assets.bulk-view') }}" class="btn btn-outline-primary">
            <i class="fas fa-file-import"></i>
            <span class="ps-1">Bulk Assign Assets</span>
        </a>
        <a href="{{ route('employee-assets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span class="ps-1">Assign Asset</span>
        </a>
    </div>
</div>
@endsection

@section('header')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-3">Asset Management</h1>

    <a href="{{ route('employee-assets.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        <span class="ps-1">Assign Asset</span>
    </a>
</div>
@endsection

@section('content')

<section class="row">
    <div class="col-12">

        <div class="card flex-fill">

            <div class="card-header">
                <h5 class="card-title mb-0">
                    Employee Asset DataTable
                </h5>
            </div>

            <table class="table data-table">

                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Employee Name</th>
                        <th>Assets</th>
                        <th>Status</th>
                        <th>Assigned Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($employeeAssets as $asset)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            <strong>
                                {{ $asset->employee->firstname ?? '' }}
                                {{ $asset->employee->lastname ?? '' }}
                            </strong> 
                        </td>

                        <td>
                            @if($asset->asset_details)
                                @php
                                    // Ensure array conversion if not cast automatically by the Model
                                    $details = is_string($asset->asset_details) ? json_decode($asset->asset_details, true) : $asset->asset_details;
                                @endphp

                                @foreach($details as $item)
                                    <div class="mb-3 pb-2 @if(!$loop->last) border-bottom border-light @endif">
                                        <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                            <span class="badge bg-primary">
                                                {{ $item['asset'] ?? $asset->asset_name }}
                                            </span>
                                            <span class="badge bg-secondary">
                                                Qty: {{ $item['qty'] ?? 1 }}
                                            </span>
                                        </div>

                                        @if(!empty($item['items']))
                                            <div class="ps-2 mt-1 text-muted row g-1" style="font-size: 0.825rem; max-width: 550px;">
                                                @foreach($item['items'] as $index => $subItem)
                                                    <div class="col-12 d-flex flex-wrap align-items-center gap-1 mb-1">
<span class="text-secondary font-monospace fw-bold">#{{ sprintf('%02d', $index + 1) }}:</span>                                                        
                                                        @if(isset($subItem['serial_no']))
                                                            <span class="bg-light px-2 py-0.5 rounded border small text-dark">SN: <strong class="font-monospace">{{ $subItem['serial_no'] }}</strong></span>
                                                        @endif

                                                        @if(isset($subItem['cpu_serial_no']))
                                                            <span class="bg-light px-2 py-0.5 rounded border small text-dark">CPU: <strong class="font-monospace">{{ $subItem['cpu_serial_no'] }}</strong></span>
                                                        @endif

                                                        @if(isset($subItem['monitor_serial_no']))
                                                            <span class="bg-light px-2 py-0.5 rounded border small text-dark">Mon: <strong class="font-monospace">{{ $subItem['monitor_serial_no'] }}</strong></span>
                                                        @endif

                                                        @if(isset($subItem['brand']))
                                                            <span class="bg-light px-2 py-0.5 rounded border small text-dark">Brand: <strong>{{ $subItem['brand'] }}</strong></span>
                                                        @endif

                                                        @if(isset($subItem['model']))
                                                            <span class="bg-light px-2 py-0.5 rounded border small text-dark">Model: <strong>{{ $subItem['model'] }}</strong></span>
                                                        @endif

                                                        @if(isset($subItem['network']))
                                                            <span class="bg-light px-2 py-0.5 rounded border small text-dark">Net: <strong>{{ $subItem['network'] }}</strong></span>
                                                        @endif

                                                        @if(isset($subItem['ram_rom']))
                                                            <span class="bg-light px-2 py-0.5 rounded border small text-dark">RAM/ROM: <strong>{{ $subItem['ram_rom'] }}</strong></span>
                                                        @endif

                                                        @if(isset($subItem['sim_number']))
                                                            <span class="bg-light px-2 py-0.5 rounded border small text-dark">SIM No: <strong class="font-monospace text-danger">{{ $subItem['sim_number'] }}</strong></span>
                                                        @endif

                                                        @if(isset($subItem['imei_1']))
                                                            <span class="bg-light px-2 py-0.5 rounded border small text-dark">IMEI 1: <strong class="font-monospace">{{ $subItem['imei_1'] }}</strong></span>
                                                        @endif

                                                        @if(isset($subItem['imei_2']))
                                                            <span class="bg-light px-2 py-0.5 rounded border small text-dark">IMEI 2: <strong class="font-monospace">{{ $subItem['imei_2'] }}</strong></span>
                                                        @endif

                                                        @if(isset($subItem['charger']))
                                                            <span class="bg-light px-2 py-0.5 rounded border small text-dark">Charger: <strong>{{ $subItem['charger'] }}</strong></span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <span class="badge bg-primary">
                                    {{ $asset->asset_name }}
                                </span>
                            @endif
                        </td>

                        <td>
                            @if($asset->status == 'Assigned')
                                <span class="badge bg-success">Assigned</span>
                            @elseif($asset->status == 'Returned')
                                <span class="badge bg-info">Returned</span>
                            @elseif($asset->status == 'Damaged')
                                <span class="badge bg-warning">Damaged</span>
                            @else
                                <span class="badge bg-danger">Lost</span>
                            @endif
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($asset->assigned_date)->format('d M Y') }}
                        </td>

                        <td>
                            <div class="d-inline-flex gap-2 align-items-center">

                                <a href="{{ route('employee-assets.edit', $asset->id) }}"
                                   class="btn btn-sm action-btn btn-light-edit"
                                   title="Edit Allocation"
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="{{ route('employee-assets.requests', $asset->id) }}"
                                   class="btn btn-sm action-btn btn-light-requests"
                                   title="View Support Requests"
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-clipboard-list"></i>
                                </a>

                                <form action="{{ route('employee-assets.destroy', $asset->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <a href="#"
                                       class="btn btn-sm action-btn btn-light-delete"
                                       title="Delete Record"
                                       data-bs-toggle="tooltip"
                                       onclick="event.preventDefault(); if(confirm('Are you absolutely sure you want to delete this asset record?')) this.closest('form').submit();">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </form>

                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="text-center">
                            No Asset Records Found
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>
</section>

<style>
    .action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px !important;
        transition: all 0.2s ease-in-out;
        border: 1px solid transparent;
        background-color: #f8fafc;
    }

    .btn-light-edit {
        color: #0284c7;
        border-color: #e0f2fe;
    }
    .btn-light-edit:hover {
        background-color: #0284c7 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(2, 132, 199, 0.2);
    }

    .btn-light-requests {
        color: #d97706;
        border-color: #fef3c7;
    }
    .btn-light-requests:hover {
        background-color: #d97706 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(217, 119, 6, 0.2);
    }

    .btn-light-delete {
        color: #dc2626;
        border-color: #fee2e2;
    }
    .btn-light-delete:hover {
        background-color: #dc2626 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.2);
    }
</style>

@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection
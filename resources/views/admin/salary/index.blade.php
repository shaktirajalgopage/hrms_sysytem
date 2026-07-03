@extends('layouts.admin') {{-- Change to match your master panel layout structure --}}

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 text-dark font-weight-bold">Salary Slips</h2>
        <a href="{{ route($routePrefix . 'salary-slip.create') }}" class="btn btn-primary px-4 shadow-sm">
            <i class="fas fa-plus mr-2"></i>Create Salary Slip
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase text-secondary font-weight-bold" style="font-size: 0.85rem;">
                        <tr>
                            <th class="px-4 py-3">SL</th>
                            <th class="py-3">Employee Name</th>
                            <th class="py-3">Month</th>
                            <th class="py-3">Year</th>
                            <th class="py-3 text-right">Net Salary</th>
                            <th class="py-3">Pay Date</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($slips as $key => $slip)
                            <tr>
                                <td class="px-4 py-3 align-middle">{{ $key + 1 }}</td>
                                <td class="py-3 align-middle font-weight-bold text-dark">
                                    {{ $slip->employee->firstname }} {{ $slip->employee->lastname }}
                                    <div class="small text-muted">{{ $slip->employee->unique_id }}</div>
                                </td>
                                <td class="py-3 align-middle">{{ $slip->month }}</td>
                                <td class="py-3 align-middle">{{ $slip->year }}</td>
                                <td class="py-3 align-middle font-weight-bold text-right text-success">
                                    {{ number_format($slip->net_salary, 2) }}
                                </td>
                                <td class="py-3 align-middle">{{ $slip->pay_date ? \Carbon\Carbon::parse($slip->pay_date)->format('Y-m-d') : 'N/A' }}</td>
                                <td class="py-3 align-middle text-center">
                                    <span class="badge badge-pill {{ $slip->status === 'Paid' ? 'badge-success' : 'badge-warning' }} px-3 py-2">
                                        {{ $slip->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-middle text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route($routePrefix . 'salary-slip.show', $slip->id) }}" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route($routePrefix . 'salary-slip.edit', $slip->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route($routePrefix . 'salary-slip.pdf', $slip->id) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Download PDF"><i class="fas fa-file-pdf"></i></a>
                                        <form action="{{ route($routePrefix . 'salary-slip.destroy', $slip->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this salary slip?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                                    <p class="mb-0">No salary slips found in system database.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
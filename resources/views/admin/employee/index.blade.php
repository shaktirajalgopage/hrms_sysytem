@extends('layouts.admin')
{{-- @extends('admin') --}}

@section('title')
    {{ __('Manage Employees') }}
@endsection

@section('header')
    <div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">

            <div>
                <h3 class="mb-0 fw-bold">Manage Employee</h3>
                <small class="text-muted">Manage all employee records from here.</small>
            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">

                <a href="{{ Auth::user()->role->slug === 'super-admin'
                    ? route('employee.create')
                    : (Auth::user()->role->slug === 'administrator'
                        ? route('admin.employee.create')
                        : route('hr.employee.create')) }}"
                    class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Add New
                </a>

                <a href="{{ Auth::user()->role->slug === 'super-admin'
                    ? route('employee.import.form')
                    : (Auth::user()->role->slug === 'hr-manager'
                        ? route('hr.employee.import.form')
                        : route('admin.employee.import.form')) }}"
                    class="btn btn-success">
                    <i data-feather="upload" class="me-1"></i>
                    Bulk Import
                </a>

                <a href="{{ Auth::user()->role->slug === 'super-admin'
                    ? route('employee.export')
                    : (Auth::user()->role->slug === 'administrator'
                        ? route('admin.employee.export')
                        : route('hr.employee.export')) }}"
                    class="btn btn-outline-primary">
                    <i data-feather="download" class="me-1"></i>
                    Export
                </a>

            </div>

        </div>
    </div>
</div>
@endsection

@section('content')
    @php
        $activeEmployees = $employees->filter(fn($employee) => $employee->emp_status === 'active')->values();
        $exEmployees = $employees->filter(fn($employee) => $employee->emp_status !== 'active')->values();
    @endphp

    <section class="row">
        <div class="col-12">
            @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
            <div class="card flex-fill">
                <div class="card-header">
                    <h5 class="card-title mb-0">Employee DataTable</h5>
                </div>

                <div class="card-body">
                    <!-- Tabs: Active Employees / Ex-Employees -->
                    <ul class="nav nav-tabs" id="employeeStatusTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="active-employees-tab" data-bs-toggle="tab"
                                data-bs-target="#active-employees" type="button" role="tab"
                                aria-controls="active-employees" aria-selected="true">
                                Active Employees
                                <span class="badge bg-success ms-1">{{ $activeEmployees->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ex-employees-tab" data-bs-toggle="tab"
                                data-bs-target="#ex-employees" type="button" role="tab" aria-controls="ex-employees"
                                aria-selected="false">
                                Ex-Employees
                                <span class="badge bg-danger ms-1">{{ $exEmployees->count() }}</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="employeeStatusTabContent">
                        <!-- Active Employees -->
                        <div class="tab-pane fade show active" id="active-employees" role="tabpanel"
                            aria-labelledby="active-employees-tab">
                            <div class="table-responsive">
                                <table class="table data-table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Emp ID</th>
                                            <th>Name of Employee</th>
                                            <th>Department</th>
                                            <th>Schedule</th>
                                            <th>Date Joined</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($activeEmployees as $employee)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $employee->unique_id }}</td>
                                                <td>
                                                    <strong>{{ $employee->firstname }} {{ $employee->lastname }}</strong>
                                                </td>
                                                <td>{{ $employee->department->title }}</td>
                                                <td>{{ $employee->schedule->title }}</td>
                                                <td>{{ $employee->created_at->diffForHumans() }}</td>
                                                <td class="d-flex align-items-center">
                                                    <a href="{{ Auth::user()->role->slug === 'super-admin' ? route('employee.edit', $employee->id) : (Auth::user()->role->slug === 'administrator' ? route('admin.employee.edit', $employee->id) : route('hr.employee.edit', $employee->id)) }}"
                                                        class="btn btn-info btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ Auth::user()->role->slug === 'super-admin' ? route('employee.show', $employee->id) : (Auth::user()->role->slug === 'administrator' ? route('admin.employee.show', $employee->id) : route('hr.employee.show', $employee->id)) }}"
                                                        class="btn btn-success btn-sm ms-1 me-1">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form
                                                        action="{{ Auth::user()->role->slug === 'super-admin' ? route('employee.destroy', $employee->id) : (Auth::user()->role->slug === 'administrator' ? route('admin.employee.destroy', $employee->id) : route('hr.employee.destroy', $employee->id)) }}"
                                                        method="post">
                                                        <a href="#" class="btn btn-danger btn-sm"
                                                            onclick="del(event, this)">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    No active employees found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Ex-Employees -->
                        <div class="tab-pane fade" id="ex-employees" role="tabpanel" aria-labelledby="ex-employees-tab">
                            <div class="table-responsive">
                                <table class="table data-table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Emp ID</th>
                                            <th>Name of Employee</th>
                                            <th>Department</th>
                                            <th>Schedule</th>
                                            <th>Date Joined</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($exEmployees as $employee)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $employee->unique_id }}</td>
                                                <td>
                                                    <strong>{{ $employee->firstname }} {{ $employee->lastname }}</strong>
                                                </td>
                                                <td>{{ $employee->department->title }}</td>
                                                <td>{{ $employee->schedule->title }}</td>
                                                <td>{{ $employee->created_at->diffForHumans() }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-secondary text-uppercase">{{ $employee->emp_status }}</span>
                                                </td>
                                                <td class="d-flex align-items-center">
                                                    <a href="{{ Auth::user()->role->slug === 'super-admin' ? route('employee.edit', $employee->id) : (Auth::user()->role->slug === 'administrator' ? route('admin.employee.edit', $employee->id) : route('hr.employee.edit', $employee->id)) }}"
                                                        class="btn btn-info btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ Auth::user()->role->slug === 'super-admin' ? route('employee.show', $employee->id) : (Auth::user()->role->slug === 'administrator' ? route('admin.employee.show', $employee->id) : route('hr.employee.show', $employee->id)) }}"
                                                        class="btn btn-success btn-sm ms-1 me-1">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form
                                                        action="{{ Auth::user()->role->slug === 'super-admin' ? route('employee.destroy', $employee->id) : (Auth::user()->role->slug === 'administrator' ? route('admin.employee.destroy', $employee->id) : route('hr.employee.destroy', $employee->id)) }}"
                                                        method="post">
                                                        <a href="#" class="btn btn-danger btn-sm"
                                                            onclick="del(event, this)">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    No ex-employees found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Emp ID</th>
                            <th>Name of Employee</th>
                            <th>Department</th>
                            <th>Schedule</th>
                            <th>Date Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $employee->unique_id }}</td>
                                <td>
                                    <strong>{{ $employee->firstname }} {{ $employee->lastname }}</strong>
                                </td>
                                <td>{{ $employee->department->title }}</td>
                                <td>{{ $employee->schedule->title }}</td>
                                <td>{{ $employee->created_at->diffforhumans() }}</td>
                                <td class="d-flex align-items-center">
                                    <a href="{{ Auth::user()->role->slug === 'super-admin' ? route('employee.edit', $employee->id) : (Auth::user()->role->slug === 'administrator' ? route('admin.employee.edit', $employee->id) : route('hr.employee.edit', $employee->id)) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ Auth::user()->role->slug === 'super-admin' ? route('employee.show', $employee->id) : (Auth::user()->role->slug === 'administrator' ? route('admin.employee.show', $employee->id) : route('hr.employee.show', $employee->id)) }}"
                                        class="btn btn-success btn-sm ms-1 me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form
                                        action="{{ Auth::user()->role->slug === 'super-admin'
                                            ? route('employee.destroy', $employee->id)
                                            : (Auth::user()->role->slug === 'administrator'
                                                ? route('admin.employee.destroy', $employee->id)
                                                : route('hr.employee.destroy', $employee->id)) }}"
                                        method="POST">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this employee?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('script')
@endsection

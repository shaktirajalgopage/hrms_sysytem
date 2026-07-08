@extends('layouts.admin')

@section('title')
    {{ __('Employee Details') }}
@endsection

@section('header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Employee Profile</h2>

    <a href="{{ Auth::user()->role->slug === 'super-admin'
        ? route('employee.index')
        : (Auth::user()->role->slug === 'administrator'
            ? route('admin.employee.index')
            : route('hr.employee.index')) }}"
        class="btn btn-primary">

        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>
@endsection


@section('content')

<div class="row">

    <!-- LEFT PROFILE -->
    <div class="col-md-4">

        <div class="card shadow border-0 mb-4">
            <div class="card-body text-center">

                <img
                    src="{{ $employee->avatar ? asset('storage/' . $employee->avatar) : 'https://via.placeholder.com/300x300' }}"
                    class="rounded-circle border"
                    width="180"
                    height="180"
                    style="object-fit:cover"
                >

                <h4 class="mt-3">
                    {{ $employee->firstname }} {{ $employee->lastname }}
                </h4>

                <span class="badge bg-success">
                    {{ $employee->status }}
                </span>

                <hr>

                <p><strong>Employee ID:</strong> {{ $employee->unique_id }}</p>
                <p><strong>Email:</strong> {{ $employee->email }}</p>
                <p><strong>Phone:</strong> {{ $employee->phone }}</p>
                <p><strong>User:</strong> {{ $employee->user?->name ?? 'N/A' }}</p>

                <p>
                    <strong>Total Attendance:</strong>
                    {{ $employee->attendances->count() }}
                </p>

                <p>
                    <strong>Total Leaves:</strong>
                    {{ $employee->leaves->count() }}
                </p>

                <p>
                    <strong>Total Payrolls:</strong>
                    {{ $employee->payrolls->count() }}
                </p>

            </div>
        </div>

    </div>



    <!-- RIGHT DETAILS -->
    <div class="col-md-8">

        <!-- PERSONAL -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-primary text-white">
                Personal Information
            </div>

            <div class="card-body row">

                <div class="col-md-6 mb-2">
                    <strong>Date of Birth:</strong>
                    {{ $employee->dob }}
                </div>

                <div class="col-md-6 mb-2">
                    <strong>Gender:</strong>
                    {{ $employee->gender }}
                </div>

                <div class="col-md-6 mb-2">
                    <strong>Religion:</strong>
                    {{ $employee->religion }}
                </div>

                <div class="col-md-6 mb-2">
                    <strong>Marital Status:</strong>
                    {{ $employee->marital }}
                </div>

                <div class="col-12">
                    <strong>Address:</strong>
                    <p>{{ $employee->address }}</p>
                </div>

            </div>
        </div>


        <!-- ORGANIZATION -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-success text-white">
                Organization Details
            </div>

            <div class="card-body row">

                <div class="col-md-4">
                    <strong>Department:</strong><br>
                    {{ $employee->department?->title ?? 'N/A' }}
                </div>

                <div class="col-md-4">
                    <strong>Designation:</strong><br>
                    {{ $employee->designation?->title ?? 'N/A' }}
                </div>

                <div class="col-md-4">
                    <strong>Schedule:</strong><br>
                    {{ $employee->schedule?->title ?? 'N/A' }}
                </div>

            </div>
        </div>



        <!-- SALARY -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-warning">
                Salary Details
            </div>

            <div class="card-body row">

                <div class="col-md-4">
                    Basic: ₹{{ $employee->salary?->basic ?? 0 }}
                </div>

                <div class="col-md-4">
                    House Rent: ₹{{ $employee->salary?->house_rent ?? 0 }}
                </div>

                <div class="col-md-4">
                    Medical: ₹{{ $employee->salary?->medical ?? 0 }}
                </div>

                <div class="col-md-4 mt-3">
                    Transport: ₹{{ $employee->salary?->transport ?? 0 }}
                </div>

                <div class="col-md-4 mt-3">
                    Phone Bill: ₹{{ $employee->salary?->phone_bill ?? 0 }}
                </div>

                <div class="col-md-4 mt-3">
                    Internet: ₹{{ $employee->salary?->internet_bill ?? 0 }}
                </div>

                <div class="col-md-4 mt-3">
                    Special: ₹{{ $employee->salary?->special ?? 0 }}
                </div>

                <div class="col-md-4 mt-3">
                    PF: ₹{{ $employee->salary?->provident_fund ?? 0 }}
                </div>

                <div class="col-md-4 mt-3">
                    Tax: ₹{{ $employee->salary?->income_tax ?? 0 }}
                </div>

            </div>
        </div>



        <!-- DOCUMENTS -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-dark text-white">
                Documents
            </div>

            <div class="card-body">

                @php
                    $docs = [
                        'Aadhaar Card'=>'aadhaar_card',
                        'PAN Card'=>'pan_card',
                        'Matric Certificate'=>'matric_certificate',
                        '+2 Certificate'=>'plus_two_certificate',
                        'Bachelor Degree'=>'bachelor_degree_certificate',
                        'Master Degree'=>'master_degree_certificate',
                        'Address Proof'=>'address_proof',
                        'Release Letter'=>'last_company_release_letter',
                        'Offer Letter'=>'last_company_offer_letter',
                        'Salary Slip 1'=>'salary_slip_1',
                        'Salary Slip 2'=>'salary_slip_2',
                        'Salary Slip 3'=>'salary_slip_3',
                        'Bank Passbook'=>'bank_passbook_page'
                    ];
                @endphp

                <div class="row">

                    @foreach($docs as $label => $file)

                        <div class="col-md-6 mb-3">

                            <div class="border rounded p-3 d-flex justify-content-between">

                                <span>{{ $label }}</span>

                                @if($employee->$file)

                                    <a
                                        target="_blank"
                                        href="{{ asset('storage/'.$employee->$file) }}"
                                        class="btn btn-sm btn-primary"
                                    >
                                        View
                                    </a>

                                @else
                                    <span class="badge bg-danger">
                                        Not Uploaded
                                    </span>
                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>
        </div>



        <!-- ATTENDANCE -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-info text-white">
                Attendance History
            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($employee->attendances as $attendance)

                        <tr>
                            <td>{{ $attendance->created_at->format('d M Y') }}</td>
                            <td>{{ $attendance->status }}</td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="2">No attendance found</td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>
        </div>



        <!-- LEAVES -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header bg-danger text-white">
                Leave History
            </div>

            <div class="card-body">

                @forelse($employee->leaves as $leave)

                    <p>
                        {{ $leave->created_at->format('d M Y') }}
                        - {{ $leave->reason }}
                    </p>

                @empty
                    No leave records
                @endforelse

            </div>
        </div>



        <!-- PAYROLL -->
        <div class="card shadow border-0">
            <div class="card-header bg-secondary text-white">
                Payroll History
            </div>

            <div class="card-body">

                @forelse($employee->payrolls as $payroll)

                    <p>
                        {{ $payroll->month }}
                        — ₹{{ $payroll->amount }}
                    </p>

                @empty
                    No payroll records
                @endforelse

            </div>
        </div>

    </div>

</div>

@endsection
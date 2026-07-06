@extends('layouts.admin')

@section('title')
    {{ __('Edit Employee') }}
@endsection

@section('header')
@endsection

@section('content')
<section class="row">
    <div class="col-12">
        <form method="POST"
            action="{{ Auth::user()->role->slug === 'super-admin'
                ? route('employee.update', $employee->id)
                : (Auth::user()->role->slug === 'administrator'
                    ? route('admin.employee.update', $employee->id)
                    : route('hr.employee.update', $employee->id)) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">

                {{-- ── LEFT COLUMN ── --}}
                <div class="col-6">

                    {{-- Personal --}}
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Personal') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label for="firstname">{{ __('First Name') }}</label>
                                    <input type="text" name="firstname" id="firstname" class="form-control"
                                        value="{{ $employee->firstname }}" placeholder="John" />
                                </div>
                                <div class="col-6">
                                    <label for="lastname">{{ __('Last Name') }}</label>
                                    <input type="text" name="lastname" id="lastname" class="form-control"
                                        value="{{ $employee->lastname }}" placeholder="Doe" required />
                                </div>
                                <div class="col-12">
                                    <label for="email">{{ __('Email Address') }}</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="{{ $employee->email }}" required>
                                </div>
                                <div class="col-6">
                                    <label for="official_email">{{ __('Official Email') }}</label>
                                    <input type="email" name="official_email" id="official_email" class="form-control"
                                        value="{{ $employee->official_email }}">
                                </div>
                                <div class="col-6">
                                    <label for="personal_email">{{ __('Personal Email') }}</label>
                                    <input type="email" name="personal_email" id="personal_email" class="form-control"
                                        value="{{ $employee->personal_email }}">
                                </div>
                                <div class="col-6">
                                    <label for="phone">{{ __('Cell Phone') }}</label>
                                    <input type="tel" name="phone" class="form-control" id="phone"
                                        value="{{ $employee->phone }}" required maxlength="19" />
                                </div>
                                <div class="col-6">
                                    <label for="emergency_phone">{{ __('Emergency Phone') }}</label>
                                    <input type="tel" name="emergency_phone" class="form-control" id="emergency_phone"
                                        value="{{ $employee->emergency_phone }}" maxlength="19" />
                                </div>
                                <div class="col-6">
                                    <label for="official_phone">{{ __('Official Phone') }}</label>
                                    <input type="tel" name="official_phone" class="form-control" id="official_phone"
                                        value="{{ $employee->official_phone }}" maxlength="19" />
                                </div>
                                <div class="col-6">
                                    <label for="blood_group">{{ __('Blood Group') }}</label>
                                    <select name="blood_group" class="form-control" id="blood_group">
                                        <option value="">-- Choose --</option>
                                        @foreach (['A+ve','A-ve','B+ve','B-ve','AB+ve','AB-ve','O+ve','O-ve'] as $bg)
                                            <option value="{{ $bg }}" {{ $employee->blood_group === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="dob">{{ __('Date of Birth') }}</label>
                                    <input type="date" name="dob" class="form-control" id="dob"
                                        value="{{ $employee->dob }}" />
                                </div>
                               
                                <div class="col-6">
                                    <label for="doj">{{ __('Date of Joining') }}</label>
                                    <input type="date" name="doj" class="form-control" id="doj"
                                       value="{{ date('Y-m-d', strtotime($employee->doj)) }}">
                                </div>
                                <div class="col-12">
                                    <label for="address">{{ __('Address') }}</label>
                                    <textarea name="address" class="form-control" id="address" rows="3">{{ $employee->address }}</textarea>
                                </div>
                                <div class="col-6">
                                    <label for="present_address">{{ __('Present Address') }}</label>
                                    <textarea name="present_address" class="form-control" id="present_address" rows="3">{{ $employee->present_address }}</textarea>
                                </div>
                                <div class="col-6">
                                    <label for="permanent_address">{{ __('Permanent Address') }}</label>
                                    <textarea name="permanent_address" class="form-control" id="permanent_address" rows="3">{{ $employee->permanent_address }}</textarea>
                                </div>
                                <div class="col-4">
                                    <label for="gender">{{ __('Gender') }}</label>
                                    <select name="gender" class="form-control" id="gender">
                                        <option value="">{{ __('-- Choose One --') }}</option>
                                        <option value="1" {{ $employee->gender === 1 ? 'selected' : '' }}>{{ __('Male') }}</option>
                                        <option value="2" {{ $employee->gender === 2 ? 'selected' : '' }}>{{ __('Female') }}</option>
                                        <option value="3" {{ $employee->gender === 3 ? 'selected' : '' }}>{{ __('Others') }}</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label for="religion">{{ __('Religion') }}</label>
                                    <select name="religion" class="form-control" id="religion">
                                        <option value="">{{ __('-- Choose One --') }}</option>
                                        <option value="1" {{ $employee->religion === 1 ? 'selected' : '' }}>{{ __('Islam') }}</option>
                                        <option value="2" {{ $employee->religion === 2 ? 'selected' : '' }}>{{ __('Christian') }}</option>
                                        <option value="3" {{ $employee->religion === 3 ? 'selected' : '' }}>{{ __('Hinduism') }}</option>
                                        <option value="4" {{ $employee->religion === 4 ? 'selected' : '' }}>{{ __('Buddhist') }}</option>
                                        <option value="5" {{ $employee->religion === 5 ? 'selected' : '' }}>{{ __('Others') }}</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label for="marital">{{ __('Marital Status') }}</label>
                                    <select name="marital" class="form-control" id="marital">
                                        <option value="">{{ __('-- Choose One --') }}</option>
                                        <option value="1" {{ $employee->marital === 1 ? 'selected' : '' }}>{{ __('Married') }}</option>
                                        <option value="2" {{ $employee->marital === 2 ? 'selected' : '' }}>{{ __('Unmarried') }}</option>
                                        <option value="3" {{ $employee->marital === 3 ? 'selected' : '' }}>{{ __('Divorced') }}</option>
                                        <option value="4" {{ $employee->marital === 4 ? 'selected' : '' }}>{{ __('Widowed') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-6 d-grid">
                                    <a href="{{ Auth::user()->role->slug === 'super-admin'
                                        ? route('employee.index')
                                        : (Auth::user()->role->slug === 'administrator'
                                            ? route('admin.employee.index')
                                            : route('hr.employee.index')) }}"
                                        class="btn btn-outline-secondary">
                                        <i class="align-middle me-1" data-feather="arrow-left"></i>
                                        <span class="ps-1">{{ __('Discard') }}</span>
                                    </a>
                                </div>
                                <div class="col-6 d-grid">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="align-middle me-1" data-feather="check"></i>
                                        <span class="ps-1">{{ __('Update') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Organizational --}}
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Organizational') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="department">{{ __('Department') }}</label>
                                    <select name="department_id" class="form-control" id="department">
                                        @forelse ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ $department->id === $employee->department_id ? 'selected' : '' }}>
                                                {{ $department->title }}
                                            </option>
                                        @empty
                                            <option value="">{{ __('-- Choose One --') }}</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="designation">{{ __('Designation') }}</label>
                                    <select name="designation_id" class="form-control" id="designation">
                                        @forelse ($designations as $designation)
                                            <option value="{{ $designation->id }}"
                                                {{ $designation->id === $employee->designation_id ? 'selected' : '' }}>
                                                {{ $designation->title }}
                                            </option>
                                        @empty
                                            <option value="">{{ __('-- Choose One --') }}</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="employeeId">{{ __('Employee ID') }}</label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-secondary btn-sm" id="generate" disabled>
                                            <i class="fas fa-arrows-rotate"></i>
                                        </button>
                                        <input type="text" name="unique_id" id="employeeId"
                                            class="form-control" value="{{ $employee->unique_id }}" />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="basic">{{ __('Basic Salary') }}</label>
                                    <input type="number" name="basic" class="form-control" id="basic"
                                        value="{{ $employee->salary?->basic }}" step="0.01" required>
                                </div>
                                <div class="col-6">
                                    <label for="schedule">{{ __('Working Schedule') }}</label>
                                    <select name="schedule_id" class="form-control" id="schedule">
                                        @forelse ($schedules as $schedule)
                                            <option value="{{ $schedule->id }}"
                                                {{ $schedule->id === $employee->schedule_id ? 'selected' : '' }}>
                                                {{ $schedule->title }}
                                            </option>
                                        @empty
                                            <option value="">{{ __('--Choose Schedule--') }}</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control" id="status">
                                        <option value="">{{ __('-- Choose One --') }}</option>
                                        @foreach ([1=>'Currently Employed',2=>'Retired',3=>'Resigned',4=>'Terminated',5=>'On Leave',6=>'Contract Ended',7=>'Part-Time',8=>'Full-Time',9=>'Freelancer',10=>'Intern',11=>'Transferred'] as $val => $label)
                                            <option value="{{ $val }}" {{ $employee->status === $val ? 'selected' : '' }}>{{ __($label) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- end col-6 left --}}

                {{-- ── RIGHT COLUMN ── --}}
                <div class="col-6">

                    {{-- Allowance --}}
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Allowance') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label>House Rent</label>
                                    <input type="number" name="house_rent" class="form-control" id="rent"
                                        value="{{ $employee->salary?->house_rent }}" step="0.01" readonly />
                                </div>
                                <div class="col-6">
                                    <label>Medical</label>
                                    <input type="number" name="medical" class="form-control" id="medical"
                                        value="{{ $employee->salary?->medical }}" step="0.01" readonly />
                                </div>
                                <div class="col-6">
                                    <label>Transport</label>
                                    <input type="number" name="transport" class="form-control" id="transport"
                                        value="{{ $employee->salary?->transport }}" step="0.01" readonly />
                                </div>
                                <div class="col-6">
                                    <label>Phone Bill</label>
                                    <input type="number" name="phone_bill" class="form-control"
                                        value="{{ $employee->salary?->phone_bill }}" step="0.01" />
                                </div>
                                <div class="col-6">
                                    <label>Internet Bill</label>
                                    <input type="number" name="internet_bill" class="form-control"
                                        value="{{ $employee->salary?->internet_bill }}" step="0.01" />
                                </div>
                                <div class="col-6">
                                    <label>Special</label>
                                    <input type="number" name="special" class="form-control"
                                        value="{{ $employee->salary?->special }}" step="0.01" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Deductions --}}
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Deductions') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label>{{ __('Provident Fund') }}</label>
                                    <input type="number" name="provident_fund" class="form-control" id="providentFund"
                                        value="{{ $employee->salary?->provident_fund }}" step="0.01" readonly />
                                </div>
                                <div class="col-6">
                                    <label>{{ __('Income Tax') }}</label>
                                    <input type="number" name="income_tax" class="form-control" id="incomeTax"
                                        value="{{ $employee->salary?->income_tax }}" step="0.01" readonly />
                                </div>
                                <div class="col-6">
                                    <label>{{ __('Health Insurance') }}</label>
                                    <input type="number" name="health_insurance" class="form-control" id="healthInsurance"
                                        value="{{ $employee->salary?->health_insurance }}" step="0.01" readonly />
                                </div>
                                <div class="col-6">
                                    <label>{{ __('Life Insurance') }}</label>
                                    <input type="number" name="life_insurance" class="form-control" id="lifeInsurance"
                                        value="{{ $employee->salary?->life_insurance }}" step="0.01" readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Employee Photo --}}
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Employee Photo') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label for="imageInput"
                                        class="d-flex flex-column align-items-center justify-content-center bg-light h-100"
                                        style="border: 3px dashed lightgray; cursor:pointer;">
                                        <div class="d-flex flex-column align-items-center justify-content-center py-1">
                                            <h1 class="h1 mb-0"><i class="align-middle" data-feather="upload-cloud"></i></h1>
                                            <h6 class="my-1 text-dark text-center"><strong>{{ __('Click to upload') }}</strong></h6>
                                            <p class="mb-2 text-dark text-center" style="font-size: 0.75rem;">
                                                PNG, JPG or JPEG<br/>(MAX. 2MB)<br/>(MIN. 300×300)
                                            </p>
                                        </div>
                                        <input type="file" name="avatar" class="d-none" id="imageInput" accept="image/*" />
                                    </label>
                                </div>
                                <div class="col-6">
                                    <img id="dummy"
                                        src="{{ $employee->avatar ? asset('storage/' . $employee->avatar) : 'https://via.placeholder.com/300x300' }}"
                                        class="w-100" alt="Employee Photo" />
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- end col-6 right --}}

            </div>{{-- end row --}}

            {{-- ── Documents ── --}}
            <div class="card flex-fill mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Employee Documents') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        @php
                        $docFields = [
                            'aadhaar_card'                => 'Aadhaar Card',
                            'pan_card'                    => 'PAN Card',
                            'matric_certificate'          => 'Matric Certificate',
                            'plus_two_certificate'        => '+2 Certificate',
                            'bachelor_degree_certificate' => 'Bachelor Degree Certificate',
                            'master_degree_certificate'   => 'Master Degree Certificate',
                            'address_proof'               => 'Address Proof',
                            'last_company_release_letter' => 'Last Company Release Letter',
                            'last_company_offer_letter'   => 'Last Company Offer Letter',
                            'salary_slip_1'               => 'Salary Slip 1',
                            'salary_slip_2'               => 'Salary Slip 2',
                            'salary_slip_3'               => 'Salary Slip 3',
                            'bank_passbook_page'          => 'Bank Passbook Page',
                        ];
                        @endphp

                        @foreach ($docFields as $field => $label)
                        <div class="col-6">
                            <label>{{ $label }}</label>
                            @if ($employee->$field)
                                <div class="mb-1">
                                    <a href="{{ asset('storage/' . $employee->$field) }}" target="_blank"
                                       class="btn btn-sm btn-outline-info">
                                        <i data-feather="eye"></i> View Current
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="{{ $field }}" class="form-control">
                            <small class="text-muted">Leave blank to keep existing file.</small>
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>

            {{-- ── Hierarchy ── --}}
            <div class="col-12 mt-3">
                <div class="border rounded p-3 bg-light" id="hierarchy-section">
                    <h6 class="mb-3 fw-bold text-muted">
                        <i data-feather="git-merge" class="me-1"></i>
                        Reporting Hierarchy
                    </h6>

                    <div id="hierarchy-preview" class="alert alert-info d-none py-2 mb-3" style="font-size:0.85rem">
                        <strong>Department hierarchy:</strong>
                        <span id="preview-text">Select a department to see available employees.</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="team_lead_id">Team Lead <small class="text-muted">(direct supervisor)</small></label>
                            <select name="team_lead_id" id="team_lead_id" class="form-control">
                                <option value="">-- None --</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="manager_id">Manager</label>
                            <select name="manager_id" id="manager_id" class="form-control">
                                <option value="">-- None --</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="hr_id">HR Person</label>
                            <select name="hr_id" id="hr_id" class="form-control">
                                <option value="">-- None --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="align-middle me-1" data-feather="save"></i>
                        {{ __('Update Employee') }}
                    </button>
                </div>
            </div>

        </form>
    </div>
</section>
@endsection

@section('script')
<script>
    // ── Photo preview ──
    document.getElementById('imageInput').onchange = function () {
        const [file] = this.files;
        if (file) document.getElementById('dummy').src = URL.createObjectURL(file);
    };

    // ── Hierarchy dropdowns ──
    const deptSelect   = document.getElementById('department');
    const teamLeadSel  = document.getElementById('team_lead_id');
    const managerSel   = document.getElementById('manager_id');
    const hrSel        = document.getElementById('hr_id');
    const previewDiv   = document.getElementById('hierarchy-preview');
    const previewText  = document.getElementById('preview-text');

    // Current hierarchy values from PHP
    const currentHierarchy = {
        team_lead_id : {{ $employee->hierarchy?->team_lead_id ?? 'null' }},
        manager_id   : {{ $employee->hierarchy?->manager_id   ?? 'null' }},
        hr_id        : {{ $employee->hierarchy?->hr_id        ?? 'null' }},
    };

    function buildOptions(selectEl, employees, currentVal) {
        const prev = selectEl.value;
        selectEl.innerHTML = '<option value="">-- None --</option>';
        employees.forEach(emp => {
            const opt = document.createElement('option');
            opt.value = emp.id;
            opt.textContent = emp.name;
            if (emp.id == (currentVal !== null ? currentVal : prev)) opt.selected = true;
            selectEl.appendChild(opt);
        });
    }

    function loadDeptEmployees(deptId, presetValues) {
        if (!deptId) return;

        // Build the correct URL prefix depending on role
        @if(Auth::user()->role->slug === 'super-admin')
            const url = `/super/employees/by-department?department_id=${deptId}`;
        @elseif(Auth::user()->role->slug === 'administrator')
            const url = `/admin/employees/by-department?department_id=${deptId}`;
        @else
            const url = `/hr/employees/by-department?department_id=${deptId}`;
        @endif

        fetch(url)
            .then(r => r.json())
            .then(employees => {
                // Exclude current employee from selectable options
                const others = employees.filter(e => e.id != {{ $employee->id }});

                buildOptions(teamLeadSel, others, presetValues?.team_lead_id ?? null);
                buildOptions(managerSel,  others, presetValues?.manager_id   ?? null);
                buildOptions(hrSel,       others, presetValues?.hr_id        ?? null);

                previewText.textContent = others.length === 0
                    ? 'No other employees in this department yet.'
                    : `${others.length} employee(s) available to assign as Team Lead / Manager / HR.`;
                previewDiv.classList.remove('d-none');
            })
            .catch(() => {
                previewText.textContent = 'Could not load department employees.';
                previewDiv.classList.remove('d-none');
            });
    }

    deptSelect.addEventListener('change', function () {
        loadDeptEmployees(this.value, null);
    });

    // Auto-load on page open and pre-select current hierarchy
    if (deptSelect.value) {
        loadDeptEmployees(deptSelect.value, currentHierarchy);
    }
</script>
@endsection

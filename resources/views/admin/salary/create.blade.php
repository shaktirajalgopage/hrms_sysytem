@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp

@section('content')
<div class="container-fluid px-4 py-4 bg-white">
    <div class="mb-4 no-print d-flex justify-content-between align-items-center border-bottom pb-2">
        <a href="{{ route($routePrefix . 'salary-slip.index') }}" class="btn btn-link text-secondary p-0 font-weight-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Salary Slips Overview
        </a>
        <span class="text-muted small">Issue Engine Layer</span>
    </div>

    <form action="{{ route($routePrefix . 'salary-slip.store') }}" method="POST" id="salarySlipForm">
        @csrf
        
        <div class="payslip-box-layout border mx-auto p-4 md:p-5 bg-white shadow-sm rounded mb-5" style="max-width: 850px; color: #333;">
            
            <div class="row align-items-center mb-2">
                <div class="col-md-6 col-12 text-center text-md-left mb-3 mb-md-0">
                    <div class="brand-logo-wrapper">
    <img src="{{ asset('img/photos/app_logo.png') }}" alt="AlgoPage Logo" style="height: 50px; width: auto; object-fit: contain;">
</div>
                </div>
                <div class="col-md-6 col-12 text-center text-md-right address-text-block small text-muted" style="line-height: 1.4; font-size: 11px;">
                    <strong class="text-dark">AlgoPage Pvt. Ltd.</strong><br>
                    Plot No - A-1, Govind Vihar, Near Jaya Durga Nagar,<br>
                    Bomikhal, Jharapada, Bhubaneswar, Odisha 751010<br>
                    Phone: +91-9348222048 | Email: info@algopage.com
                </div>
            </div>

            <div class="w-100 bg-primary my-2" style="height: 3px; background-color: #0066ff !important;"></div>

            <div class="w-100 text-white text-center font-weight-bold py-2 text-uppercase rounded mb-3" 
                 style="background-color: #0066ff !important; font-size: 14px; letter-spacing: 1px;">
                Salary Slip
            </div>

            <div class="row justify-content-center mb-3 text-muted small">
                <div class="col-auto d-flex align-items-center mb-2">
                    <span class="mr-2">Pay Period:</span>
                    <select name="month" class="form-control form-control-sm d-inline-block border-0 border-bottom bg-transparent font-weight-bold text-primary p-0 h-auto" style="width: 95px; border-radius: 0;" required>
                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                            <option value="{{ $m }}" {{ date('F') == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="year" class="form-control form-control-sm d-inline-block border-0 border-bottom bg-transparent font-weight-bold text-primary p-0 h-auto ml-1" style="width: 55px; border-radius: 0;" value="{{ date('Y') }}" required>
                </div>
                <div class="col-auto d-none d-md-block text-gray-300">|</div>
                <div class="col-auto d-flex align-items-center mb-2">
                    <span class="mr-2">Pay Date:</span>
                    <input type="date" name="pay_date" class="form-control form-control-sm border-0 border-bottom bg-transparent font-weight-bold text-primary p-0 h-auto" style="border-radius: 0;" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="bg-light p-3 border rounded mb-3 position-relative no-print">
                <label class="font-weight-bold text-dark small mb-2 text-uppercase tracking-wider text-primary">Target Employee Verification Lookup <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white text-muted border-right-0"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" id="employee_search" autocomplete="off" class="form-control border-left-0" placeholder="Type to look up employee via ID, full name, or system email descriptor rules...">
                </div>
                <input type="hidden" name="employee_id" id="employee_id" value="{{ old('employee_id') }}">
                <div id="suggestions_box" class="list-group position-absolute w-100 shadow-lg d-none mt-1" style="z-index: 1050; max-height: 220px; overflow-y: auto;"></div>
                @error('employee_id') <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small> @enderror
            </div>

            <table class="table table-sm table-bordered meta-profile-grid-table mb-4" style="font-size: 12px; table-layout: fixed; width: 100%;">
                <tbody>
                    <tr>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle" style="width: 22%;">Employee Name</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle" style="width: 28%;"><span id="lbl_name">-</span></td>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle" style="width: 22%;">Employee ID</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle" style="width: 28%;"><input type="text" id="meta_code" class="form-control-plaintext font-weight-bold p-0 text-dark" style="font-size: 12px;" readonly value="-"></td>
                    </tr>
                    <tr>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Designation</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle"><input type="text" id="meta_desig" class="form-control-plaintext font-weight-bold p-0 text-dark" style="font-size: 12px;" readonly value="-"></td>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Department</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle"><input type="text" id="meta_dept" class="form-control-plaintext font-weight-bold p-0 text-dark" style="font-size: 12px;" readonly value="-"></td>
                    </tr>
                    <tr>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Date of Joining</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle"><input type="text" id="meta_doj" class="form-control-plaintext font-weight-bold p-0 text-dark" style="font-size: 12px;" readonly value="-"></td>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">PAN</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle text-uppercase"><input type="text" name="pan_number" class="form-control border-0 p-0 font-weight-bold bg-transparent text-dark" style="font-size: 12px;" value="ABCDE1234F"></td>
                    </tr>
                    <tr>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Bank Name</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle"><input type="text" name="bank_name" class="form-control border-0 p-0 font-weight-bold bg-transparent text-dark" style="font-size: 12px;" value="HDFC Bank"></td>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Bank A/C No.</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle"><input type="text" name="bank_account" class="form-control border-0 p-0 font-weight-bold bg-transparent text-dark" style="font-size: 12px;" value="XXXXXXXX4821"></td>
                    </tr>
                </tbody>
            </table>

            <div class="row no-gutters border rounded mb-4" style="overflow: hidden;">
                
                <div class="col-md-6 border-right">
                    <table class="table table-sm table-borderless mb-0 financial-sub-ledger-table">
                        <thead>
                            <tr class="bg-light border-bottom">
                                <th class="text-primary font-weight-bold py-2 px-3" style="font-size: 12px; letter-spacing: 0.5px;">EARNINGS</th>
                                <th class="text-right text-primary font-weight-bold py-2 px-3" style="font-size: 12px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Basic Salary</td>
                                <td class="px-3 py-1 text-right align-middle" style="width: 45%;">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="basic_salary" id="basic_salary" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent earning-calc" value="0.00">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">House Rent Allowance</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="house_rent_allowance" id="house_rent_allowance" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent earning-calc" value="0.00">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Conveyance Allowance</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="conveyance_allowance" id="conveyance_allowance" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent earning-calc" value="0.00">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Medical Allowance</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="medical_allowance" id="medical_allowance" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent earning-calc" value="0.00">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Special Allowance</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="special_allowance" id="special_allowance" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent earning-calc" value="0.00">
                                    </div>
                                </td>
                            </tr>
                            <tr class="bg-light font-weight-bold border-top">
                                <td class="px-3 py-3 align-middle text-dark" style="font-size: 13px;">Gross Earnings</td>
                                <td class="px-3 py-3 text-right align-middle font-weight-black h6 text-dark mb-0">
                                    ₹ <input type="number" step="0.01" name="gross_earnings" id="gross_earnings" class="form-control-plaintext d-inline-block text-right font-weight-bold text-dark p-0 w-75 m-0" style="height:auto;" readonly value="0.00">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0 financial-sub-ledger-table">
                        <thead>
                            <tr class="bg-light border-bottom">
                                <th class="text-secondary font-weight-bold py-2 px-3" style="color: #555 !important; font-size: 12px; letter-spacing: 0.5px;">DEDUCTIONS</th>
                                <th class="text-right text-secondary font-weight-bold py-2 px-3" style="font-size: 12px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Provident Fund</td>
                                <td class="px-3 py-1 text-right align-middle" style="width: 45%;">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="provident_fund" id="provident_fund" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent deduction-calc" value="0.00">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Professional Tax</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="professional_tax" id="professional_tax" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent deduction-calc" value="0.00">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">TDS (Income Tax)</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="income_tax" id="income_tax" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent deduction-calc" value="0.00">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Other Deductions</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="other_deductions" id="other_deductions" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent deduction-calc" value="0.00">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">&nbsp;</td>
                                <td class="px-3 py-1 text-right align-middle">&nbsp;</td>
                            </tr>
                            <tr class="bg-light font-weight-bold border-top">
                                <td class="px-3 py-3 align-middle text-dark" style="font-size: 13px;">Total Deductions</td>
                                <td class="px-3 py-3 text-right align-middle font-weight-black h6 text-dark mb-0">
                                    ₹ <input type="number" step="0.01" name="total_deductions" id="total_deductions" class="form-control-plaintext d-inline-block text-right font-weight-bold text-dark p-0 w-75 m-0" style="height:auto;" readonly value="0.00">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row align-items-center mx-0 rounded py-3 px-4 text-white font-weight-bold mb-4" 
                 style="background-color: #0066ff !important;">
                <div class="col-md-6 col-12 text-center text-md-left mb-2 mb-md-0 p-0">
                    <div style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Net Pay</div>
                    <div class="font-weight-normal text-white-50 small" style="opacity: 0.75; font-weight: 400; font-size: 10px;">Gross Earnings – Total Deductions</div>
                </div>
                <div class="col-md-6 col-12 text-center text-md-right p-0 font-weight-black" style="font-size: 26px;">
                    ₹ <span id="display_net_salary">0.00</span>
                    <input type="hidden" name="net_salary" id="net_salary" value="0.00">
                </div>
            </div>

            <div class="row bg-light border p-3 rounded mx-0 mb-4 no-print">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-secondary">Ledger Disbursal Status</label>
                    <select name="status" class="form-control form-control-sm font-weight-semibold">
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="small font-weight-bold text-secondary">Internal Remarks Layer Attachment Note</label>
                    <input type="text" name="remarks" class="form-control form-control-sm" placeholder="Add custom compliance context references or internal transactional logging remarks...">
                </div>
            </div>

            <table class="table table-sm table-borderless mt-5 signature-block-table" style="width: 100%; border:0;">
                <tbody>
                    <tr>
                        <td class="text-center text-muted small py-4" style="width: 50%; font-size: 12px; border:0;">
                            <div class="mx-auto border-top text-dark" style="width: 180px; border-color: #bbb !important; padding-top: 6px;">Employee Signature</div>
                        </td>
                        <td class="text-center text-muted small py-4" style="width: 50%; font-size: 12px; border:0;">
                            <div class="mx-auto border-top text-dark" style="width: 180px; border-color: #bbb !important; padding-top: 6px;">Authorized Signatory</div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="w-100 text-center text-muted border-top pt-2 mt-4 text-block-footer" style="font-size: 10px; line-height: 1.5; color:#999 !important;">
                This is a system-generated salary slip and is valid without a physical signature.<br>
                AlgoPage Pvt. Ltd. · Plot No - A-1, Govind Vihar, Bomikhal, Jharapada, Bhubaneswar, Odisha 751010 · info@algopage.com
            </div>
        </div>

        <div class="d-flex justify-content-end mb-5 no-print" style="max-width: 850px; margin: 0 auto;">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow font-weight-bold" style="background-color: #0066ff !important; border-color: #0066ff !important; border-radius: 4px;">
                <i class="fas fa-save mr-2"></i> Issue & Store Payslip Record
            </button>
        </div>
    </form>
</div>

<style>
    .meta-profile-grid-table td { border-color: #e2e8f0 !important; }
    .financial-sub-ledger-table td, .financial-sub-ledger-table th { border-color: #e2e8f0 !important; }
    .form-control:focus { box-shadow: none !important; border-color: #0066ff !important; }
    .list-group-item-action:hover { background-color: #f8fafc; color: #0066ff !important; }
    @media print {
        .no-print { display: none !important; }
        body { padding: 0 !important; background-color: #fff !important; }
        .payslip-box-layout { border: 0 !important; box-shadow: none !important; padding: 0 !important; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('employee_search');
    const box = document.getElementById('suggestions_box');

    searchInput.addEventListener('input', function() {
        let val = this.value.trim();
        if(val.length < 1) {
            box.classList.add('d-none');
            return;
        }

        fetch(`{{ route($routePrefix . 'salary-slip.search-employee') }}?term=${encodeURIComponent(val)}`)
            .then(res => res.json())
            .then(data => {
                box.innerHTML = '';
                if(data.length === 0) {
                    box.innerHTML = `<div class="list-group-item disabled text-muted small py-3">No verified matching record exists</div>`;
                    box.classList.remove('d-none');
                    return;
                }
                data.forEach(item => {
                    let div = document.createElement('a');
                    div.href = "#";
                    div.className = "list-group-item list-group-item-action py-3 d-flex justify-content-between align-items-center text-dark border-bottom";
                    div.innerHTML = `<div><strong class="text-primary mr-2">${item.unique_id}</strong> — ${item.firstname} ${item.lastname}</div> <span class="badge badge-light border text-muted px-2 py-1">${item.department}</span>`;
                    
                    div.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        document.getElementById('employee_id').value = item.id;
                        searchInput.value = `${item.unique_id} - ${item.firstname} ${item.lastname}`;
                        box.classList.add('d-none');

                        // Dynamic text and input labels hydration mapping
                        document.getElementById('lbl_name').innerText = `${item.firstname} ${item.lastname}`;
                        document.getElementById('meta_code').value = item.unique_id;
                        document.getElementById('meta_dept').value = item.department;
                        document.getElementById('meta_desig').value = item.designation;
                        document.getElementById('meta_doj').value = item.doj;

                        // Balance allowances logic injection
                        document.getElementById('basic_salary').value = parseFloat(item.basic || 0).toFixed(2);
                        document.getElementById('house_rent_allowance').value = parseFloat(item.house_rent || 0).toFixed(2);
                        document.getElementById('medical_allowance').value = parseFloat(item.medical || 0).toFixed(2);
                        document.getElementById('conveyance_allowance').value = parseFloat(item.transport || 0).toFixed(2);
                        document.getElementById('special_allowance').value = parseFloat(item.special || 0).toFixed(2);
                        document.getElementById('provident_fund').value = parseFloat(item.provident_fund || 0).toFixed(2);
                        document.getElementById('income_tax').value = parseFloat(item.income_tax || 0).toFixed(2);

                        calculateTotals();
                    });
                    box.appendChild(div);
                });
                box.classList.remove('d-none');
            });
    });

    document.addEventListener('click', function(e) {
        if(e.target !== searchInput) box.classList.add('d-none');
    });

    document.querySelectorAll('.earning-calc, .deduction-calc').forEach(input => {
        input.addEventListener('input', calculateTotals);
    });

    function calculateTotals() {
        let gross = 0;
        let deds = 0;

        document.querySelectorAll('.earning-calc').forEach(i => gross += parseFloat(i.value || 0));
        document.querySelectorAll('.deduction-calc').forEach(i => deds += parseFloat(i.value || 0));

        let net = gross - deds;
        if(net < 0) net = 0;

        document.getElementById('gross_earnings').value = gross.toFixed(2);
        document.getElementById('total_deductions').value = deds.toFixed(2);
        document.getElementById('net_salary').value = net.toFixed(2);
        document.getElementById('display_net_salary').innerText = net.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
});
</script>
@endsection
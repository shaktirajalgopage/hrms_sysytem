@extends('layouts.admin')

@php
    $routePrefix = auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
@endphp

@section('content')
<div class="container-fluid px-4 py-4 bg-white">
    <div class="mb-4 no-print d-flex justify-content-between align-items-center border-bottom pb-2">
        <a href="{{ route("{$routePrefix}salary-slip.index") }}" class="btn btn-link text-secondary p-0 font-weight-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Salary Slips Overview
        </a>
        <span class="text-muted small">Modification Engine Layer</span>
    </div>

    <form action="{{ route("{$routePrefix}salary-slip.update", $slip->id) }}" method="POST" id="salarySlipForm">
        @csrf
        @method('PUT')
        
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
                            <option value="{{ $m }}" {{ $slip->month == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="year" class="form-control form-control-sm d-inline-block border-0 border-bottom bg-transparent font-weight-bold text-primary p-0 h-auto ml-1" style="width: 55px; border-radius: 0;" value="{{ $slip->year }}" required>
                </div>
                <div class="col-auto d-none d-md-block text-gray-300">|</div>
                <div class="col-auto d-flex align-items-center mb-2">
                    <span class="mr-2">Pay Date:</span>
                    <input type="date" name="pay_date" class="form-control form-control-sm border-0 border-bottom bg-transparent font-weight-bold text-primary p-0 h-auto" style="border-radius: 0;" value="{{ $slip->pay_date }}">
                </div>
            </div>

            <table class="table table-sm table-bordered meta-profile-grid-table mb-4" style="font-size: 12px; table-layout: fixed; width: 100%;">
                <tbody>
                    <tr>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle" style="width: 22%;">Employee Name</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle" style="width: 28%;">{{ $slip->employee->firstname }} {{ $slip->employee->lastname }}</td>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle" style="width: 22%;">Employee ID</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle" style="width: 28%;">{{ $slip->employee->unique_id }}</td>
                    </tr>
                    <tr>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Designation</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle">{{ $slip->employee->designation?->title ?? 'N/A' }}</td>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Department</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle">{{ $slip->employee->department?->title ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Date of Joining</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle">
                            {{ $slip->employee->doj ? \Carbon\Carbon::parse($slip->employee->doj)->format('d M Y') : 'N/A' }}
                        </td>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">PAN</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle text-uppercase">{{ $slip->employee->pan_number ?? 'ABCDE1234F' }}</td>
                    </tr>
                    <tr>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Bank Name</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle">{{ $slip->employee->bank_name ?? 'HDFC Bank' }}</td>
                        <td class="bg-light text-muted font-weight-normal py-2 px-3 align-middle">Bank A/C No.</td>
                        <td class="font-weight-bold py-2 px-3 text-dark align-middle">{{ $slip->employee->bank_account_number ?? 'XXXXXXXX4821' }}</td>
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
                                        <input type="number" step="0.01" name="basic_salary" id="basic_salary" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent earning-calc" value="{{ $slip->basic_salary }}">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">House Rent Allowance</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="house_rent_allowance" id="house_rent_allowance" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent earning-calc" value="{{ $slip->house_rent_allowance }}">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Conveyance Allowance</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="conveyance_allowance" id="conveyance_allowance" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent earning-calc" value="{{ $slip->conveyance_allowance }}">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Medical Allowance</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="medical_allowance" id="medical_allowance" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent earning-calc" value="{{ $slip->medical_allowance }}">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Special Allowance</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="special_allowance" id="special_allowance" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent earning-calc" value="{{ $slip->special_allowance }}">
                                    </div>
                                </td>
                            </tr>
                            <tr class="bg-light font-weight-bold border-top">
                                <td class="px-3 py-3 align-middle text-dark" style="font-size: 13px;">Gross Earnings</td>
                                <td class="px-3 py-3 text-right align-middle font-weight-black h6 text-dark mb-0">
                                    ₹ <input type="number" step="0.01" name="gross_earnings" id="gross_earnings" class="form-control-plaintext d-inline-block text-right font-weight-bold text-dark p-0 w-75 m-0" style="height:auto;" readonly value="{{ $slip->gross_earnings }}">
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
                                        <input type="number" step="0.01" name="provident_fund" id="provident_fund" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent deduction-calc" value="{{ $slip->provident_fund }}">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Professional Tax</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="professional_tax" id="professional_tax" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent deduction-calc" value="{{ $slip->professional_tax }}">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">TDS (Income Tax)</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="income_tax" id="income_tax" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent deduction-calc" value="{{ $slip->income_tax }}">
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="px-3 py-2 align-middle font-weight-medium small text-secondary">Other Deductions</td>
                                <td class="px-3 py-1 text-right align-middle">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="small font-weight-bold mr-1 text-dark">₹</span>
                                        <input type="number" step="0.01" name="other_deductions" id="other_deductions" class="form-control form-control-sm text-right font-weight-bold text-dark border-0 p-0 bg-transparent deduction-calc" value="{{ $slip->other_deductions }}">
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
                                    ₹ <input type="number" step="0.01" name="total_deductions" id="total_deductions" class="form-control-plaintext d-inline-block text-right font-weight-bold text-dark p-0 w-75 m-0" style="height:auto;" readonly value="{{ $slip->total_deductions }}">
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
                    ₹ <span id="display_net_salary">{{ number_format($slip->net_salary, 2, '.', ',') }}</span>
                    <input type="hidden" name="net_salary" id="net_salary" value="{{ $slip->net_salary }}">
                </div>
            </div>

            <div class="row bg-light border p-3 rounded mx-0 mb-4 no-print">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-secondary">Ledger Disbursal Status</label>
                    <select name="status" class="form-control form-control-sm font-weight-semibold">
                        <option value="Pending" {{ $slip->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Paid" {{ $slip->status === 'Paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="small font-weight-bold text-secondary">Internal Remarks Layer Attachment Note</label>
                    <input type="text" name="remarks" class="form-control form-control-sm" value="{{ $slip->remarks }}" placeholder="Add custom compliance context references or internal transactional logging remarks...">
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
                <i class="fas fa-save mr-2"></i> Update & Save Slip Record
            </button>
        </div>
    </form>
</div>

<style>
    .meta-profile-grid-table td { border-color: #e2e8f0 !important; }
    .financial-sub-ledger-table td, .financial-sub-ledger-table th { border-color: #e2e8f0 !important; }
    .form-control:focus { box-shadow: none !important; border-color: #0066ff !important; }
    @media print {
        .no-print { display: none !important; }
        body { padding: 0 !important; background-color: #fff !important; }
        .payslip-box-layout { border: 0 !important; box-shadow: none !important; padding: 0 !important; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Run an initial load calculation loop to build correct gross/deduction states
    calculateTotals();

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
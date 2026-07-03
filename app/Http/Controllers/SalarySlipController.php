<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalarySlip;
use App\Http\Requests\StoreSalarySlipRequest;
use App\Http\Requests\UpdateSalarySlipRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SalarySlipController extends Controller
{



private function routePrefix()
{
    return auth()->user()->role->slug === 'hr-manager'
        ? 'hr.'
        : '';
}
    public function index()
    {
        $slips = SalarySlip::with('employee.department', 'employee.designation')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.salary.index', compact('slips'));
    }

    public function create()
    {
        return view('admin.salary.create');
    }

    public function store(StoreSalarySlipRequest $request)
    {
        SalarySlip::create($request->validated());
        return redirect()->route($this->routePrefix() . 'salary-slip.index')->with('success', 'Salary Slip created successfully.');
    }

    public function show($id)
    {
        $slip = SalarySlip::with('employee.department', 'employee.designation')->findOrFail($id);
        return view('admin.salary.show', compact('slip'));
    }

    public function edit($id)
    {
        $slip = SalarySlip::with('employee.department', 'employee.designation')->findOrFail($id);
        return view('admin.salary.edit', compact('slip'));
    }

    public function update(UpdateSalarySlipRequest $request, $id)
    {
        $slip = SalarySlip::findOrFail($id);
        $slip->update($request->validated());
        return redirect()->route($this->routePrefix() . 'salary-slip.index')->with('success', 'Salary Slip updated successfully.');
    }

    public function destroy($id)
    {
        $slip = SalarySlip::findOrFail($id);
        $slip->delete();
        return back()->with('success', 'Salary Slip deleted successfully.');
    }

    /**
     * AJAX/API Search handler endpoint for real-time live suggestions while typing.
     */
    public function searchEmployee(Request $request)
    {
        $search = $request->get('term');
        
        $employees = Employee::where('unique_id', 'LIKE', "%{$search}%")
            ->orWhere('firstname', 'LIKE', "%{$search}%")
            ->orWhere('lastname', 'LIKE', "%{$search}%")
            ->orWhere('email', 'LIKE', "%{$search}%")
            ->with(['department', 'designation', 'salary'])
            ->get();

        $results = [];
        foreach ($employees as $emp) {
            $results[] = [
                'id' => $emp->id,
                'text' => $emp->unique_id . ' - ' . $emp->firstname . ' ' . $emp->lastname,
                'firstname' => $emp->firstname,
                'lastname' => $emp->lastname,
                'unique_id' => $emp->unique_id,
                'department' => $emp->department?->title ?? 'N/A',
                'designation' => $emp->designation?->title ?? 'N/A',
                'doj' => $emp->doj ?? 'N/A',
                // Using existing app/Models/Salary attributes if available
                'basic' => $emp->salary?->basic ?? 0,
                'house_rent' => $emp->salary?->house_rent ?? 0,
                'medical' => $emp->salary?->medical ?? 0,
                'transport' => $emp->salary?->transport ?? 0,
                'special' => $emp->salary?->special ?? 0,
                'provident_fund' => $emp->salary?->provident_fund ?? 0,
                'income_tax' => $emp->salary?->income_tax ?? 0,
            ];
        }

        return response()->json($results);
    }

    /**
     * Professional Direct PDF print compiler execution view.
     */
   public function downloadPDF($id)
{
    $slip = SalarySlip::with(['employee.department', 'employee.designation'])->findOrFail($id);
    
    // Generate PDF using DomPDF facade extension engine layout
    $pdf = Pdf::loadView('admin.salary.pdf', compact('slip'));
    $pdf->setPaper('A4', 'portrait');
    
    return $pdf->download("Payslip_{$slip->employee->unique_id}_{$slip->month}.pdf");
}

    /**
 * Display a listing of the logged-in employee's own salary slips.
 */
public function mySalarySlips()
{
    // Ensure the user has an associated employee profile record
    $employee = auth()->user()->employee;

    if (!$employee) {
        return redirect()->back()->with('error', 'Employee profile record not found for this account.');
    }

    $slips = SalarySlip::where('employee_id', $employee->id)
        ->orderBy('year', 'desc')
        ->orderByRaw("FIELD(month, 'December', 'November', 'October', 'September', 'August', 'July', 'June', 'May', 'April', 'March', 'February', 'January')")
        ->get();

    return view('admin.salary.my_slips', compact('slips', 'employee'));
}
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeHierarchy;
use App\Models\Salary;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function index()
    {
        $employees = Employee::all();
        return view('admin.employee.index', compact('employees'));
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function create()
    {
        $departments  = Department::all();
        $designations = Designation::all();
        $schedules    = Schedule::all();

        return view('admin.employee.create', compact('departments', 'designations', 'schedules'));
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(StoreEmployeeRequest $request)
    {
        $salaryFields = [
            'basic', 'house_rent', 'medical', 'transport',
            'phone_bill', 'internet_bill', 'special',
            'provident_fund', 'income_tax', 'health_insurance', 'life_insurance',
        ];

        $data = $request->except(array_merge(['_token'], $salaryFields));

        $data = $this->handleFileUploads($request, $data);

        $user = User::create([
            'role_id'  => 8,
            'name'     => $request->firstname . ' ' . $request->lastname,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'status'   => 1,
            'password' => Hash::make('Emp@1234'),
        ]);

        if ($user) {
            $data['user_id'] = $user->id;
            $employee = Employee::create($data);
        }

        if (isset($employee) && $employee) {
            $salaryData = $request->only($salaryFields);
            $employee->salary()->create($salaryData);

            EmployeeHierarchy::create([
                'employee_id' => $employee->id,
                'team_lead_id' => $request->team_lead_id ?: null,
                'manager_id'   => $request->manager_id   ?: null,
                'hr_id'        => $request->hr_id         ?: null,
            ]);
        }

        return redirect()->back()->with('success', 'Employee created successfully.');
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function show($id)
    {
        $employee = Employee::with([
            'department', 'designation', 'schedule',
            'salary', 'attendances', 'leaves', 'payrolls', 'user', 'hierarchy',
        ])->findOrFail($id);

        $departments  = Department::all();
        $designations = Designation::all();
        $schedules    = Schedule::all();

        return view('admin.employee.show', compact('employee', 'departments', 'designations', 'schedules'));
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function edit($id)
    {
        $employee = Employee::with(['salary', 'hierarchy'])->findOrFail($id);

     //    dd($employee);

        $departments  = Department::all();
        $designations = Designation::all();
        $schedules    = Schedule::all();

        return view('admin.employee.edit', compact('employee', 'departments', 'designations', 'schedules'));
    }

    // ─────────────────────────────────────────────
    // UPDATE  ← fixed: now mirrors store() fully
    // ─────────────────────────────────────────────
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        // dd($request->all());
        $salaryFields = [
            'basic', 'house_rent', 'medical', 'transport',
            'phone_bill', 'internet_bill', 'special',
            'provident_fund', 'income_tax', 'health_insurance', 'life_insurance',
        ];

        $data = $request->except(array_merge(['_token', '_method'], $salaryFields));

        // Handle file uploads — only replace if a new file is provided
        $data = $this->handleFileUploads($request, $data);

        $employee->update($data);

        // Salary
        $salary = $employee->salary ?: new Salary(['employee_id' => $employee->id]);
        $salary->fill($request->only($salaryFields));
        $employee->salary()->save($salary);

        // Hierarchy — update or create
        EmployeeHierarchy::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'team_lead_id' => $request->team_lead_id ?: null,
                'manager_id'   => $request->manager_id   ?: null,
                'hr_id'        => $request->hr_id         ?: null,
            ]
        );

        // Sync User record (name / email / phone)
        if ($employee->user) {
            $employee->user->update([
                'name'  => $request->firstname . ' ' . $request->lastname,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);
        }

        return back()->with('success', 'Employee updated successfully.');
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return back()->with('success', 'Employee deleted successfully.');
    }

    // ─────────────────────────────────────────────
    // AJAX: employees by department
    // ─────────────────────────────────────────────
    public function getByDepartment(Request $request)
    {
        $employees = Employee::where('department_id', $request->department_id)
            ->orWhere('department_id', 2)
            ->select('id', 'firstname', 'lastname')
            ->get()
            ->map(fn ($e) => ['id' => $e->id, 'name' => $e->firstname . ' ' . $e->lastname]);

        return response()->json($employees);
    }

    public function getHrDepartmentData(Request $request)
    {
        $employees = Employee::where('department_id', '2')
            ->select('id', 'firstname', 'lastname')
            ->get()
            ->map(fn ($e) => ['id' => $e->id, 'name' => $e->firstname . ' ' . $e->lastname]);

        return response()->json($employees);
    }

    public function hierarchyChain(Employee $employee)
    {
        $hierarchy = EmployeeHierarchy::where('employee_id', $employee->id)->first();
        $chain = [];

        if ($hierarchy) {
            if ($hierarchy->team_lead_id) {
                $tl = Employee::find($hierarchy->team_lead_id);
                $chain[] = 'Team Lead: ' . $tl?->firstname . ' ' . $tl?->lastname;
            }
            if ($hierarchy->manager_id) {
                $mgr = Employee::find($hierarchy->manager_id);
                $chain[] = 'Manager: ' . $mgr?->firstname . ' ' . $mgr?->lastname;
            }
            if ($hierarchy->hr_id) {
                $hr = Employee::find($hierarchy->hr_id);
                $chain[] = 'HR: ' . $hr?->firstname . ' ' . $hr?->lastname;
            }
        }

        return response()->json(['chain' => $chain]);
    }

    // ─────────────────────────────────────────────
    // BULK IMPORT — show form
    // ─────────────────────────────────────────────
    public function bulkImportForm()
    {
        return view('admin.employee.bulk_import');
    }

    // ─────────────────────────────────────────────
    // BULK IMPORT — process upload
    // ─────────────────────────────────────────────
    public function bulkImport(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('excel_file');

        // Use PhpSpreadsheet (already a Laravel/PhpOffice dependency)
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, true); // indexed by column letters

        // Remove header row
        $headers = array_shift($rows);

        // Normalise header => column-letter map
        $colMap = [];
        foreach ($headers as $col => $header) {
            if ($header !== null) {
                $colMap[strtolower(trim((string)$header))] = $col;
            }
        }

        $imported = 0;
        $skipped  = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $rowNum = $index + 2; // Excel row number

                $get = fn(string $key) => isset($colMap[$key]) ? trim((string)($row[$colMap[$key]] ?? '')) : '';

                // ── Parse name ──
                $fullName  = $get('employee name');
                $nameParts = explode(' ', $fullName, 2);
                $firstname = $nameParts[0] ?? '';
                $lastname  = $nameParts[1] ?? '';

                if (empty($firstname)) {
                    $skipped[] = "Row $rowNum: Empty employee name — skipped.";
                    continue;
                }

                // ── Resolve department ──
                $deptTitle  = $get('department ') ?: $get('department');
                $department = Department::firstOrCreate(['title' => $deptTitle]);

                // ── Resolve designation ──
                $desigTitle  = $get('designation');
                $designation = Designation::firstOrCreate(['title' => $desigTitle]);

                // ── Emails ──
                $officialEmail = $get('official gmail ') ?: $get('official gmail');
                $personalEmail = $get('personal gmail');

                // Primary email for login (official first, fallback personal, fallback generated)
                $loginEmail = $officialEmail ?: $personalEmail
                    ?: Str::slug($fullName) . '.' . time() . '@imported.local';

                // Skip if email already exists
                if (User::where('email', $loginEmail)->exists()) {
                    $skipped[] = "Row $rowNum: Email $loginEmail already exists — skipped.";
                    continue;
                }

                // ── Password ──
                // Use excel password column if present; otherwise generate
                $rawPassword = $get('password') ?: $get('password.1');
                if (empty($rawPassword)) {
                    $rawPassword = 'Emp@' . Str::random(8);
                }

                // ── Dates ──
                $dob = $this->parseExcelDate($get('d.o.b'));
                $doj = $this->parseExcelDate($get('d.o.j'));

                // ── Create User ──
                $user = User::create([
                    'role_id'  => 8,
                    'name'     => $fullName,
                    'email'    => $loginEmail,
                    'phone'    => $get(' ph number') ?: $get('ph number'),
                    'status'   => 1,
                    'password' => Hash::make($rawPassword),
                ]);

                // ── Create Employee ──
                $uniqueId = $get('employee code') ?: strtoupper('EMP' . Str::random(6));

                // Fallback to first available schedule so NOT NULL constraint is satisfied
                $defaultScheduleId = Schedule::first()?->id;

                $employee = Employee::create([
                    'user_id'          => $user->id,
                    'department_id'    => $department->id,
                    'designation_id'   => $designation->id,
                    'schedule_id'      => $defaultScheduleId,
                    'firstname'        => $firstname,
                    'lastname'         => $lastname,
                    'unique_id'        => $uniqueId,
                    'email'            => $loginEmail,
                    'official_email'   => $officialEmail,
                    'personal_email'   => $personalEmail,
                    'phone'            => $get(' ph number') ?: $get('ph number'),
                    'emergency_phone'  => $get('emergency phone number'),
                    'official_phone'   => $get('official number'),
                    'dob'              => $dob,
                    'doj'              => $doj,
                    'blood_group'      => $get('blood group'),
                    'present_address'  => $get('present address'),
                    'permanent_address'=> $get('permanent address'),
                    'status'           => 1,
                ]);

                // ── Create default salary (0s) ──
                $employee->salary()->create([
                    'basic'           => 0,
                    'house_rent'      => 0,
                    'medical'         => 0,
                    'transport'       => 0,
                    'phone_bill'      => 0,
                    'internet_bill'   => 0,
                    'special'         => 0,
                    'provident_fund'  => 0,
                    'income_tax'      => 0,
                    'health_insurance'=> 0,
                    'life_insurance'  => 0,
                ]);

                $imported++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        $message = "$imported employee(s) imported successfully.";
        if (!empty($skipped)) {
            $message .= ' Skipped: ' . implode(' | ', $skipped);
        }

        return back()->with('success', $message);
    }

    // ─────────────────────────────────────────────
    // DOWNLOAD sample Excel template
    // ─────────────────────────────────────────────
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Employees');

        $headers = [
            'Employee Name', 'Department ', 'Designation', 'Employee code',
            'Blood group', ' Ph Number', 'Emergency Phone Number', 'Official Number',
            'D.O.J', 'D.O.B', 'Team Id ', 'Password',
            'Official Gmail ', 'Password.1', 'Personal Gmail',
            'Present Address', 'Permanent Address',
        ];

        foreach ($headers as $i => $header) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $cell = $sheet->getCell($col . '1');
            $cell->setValue($header);
            $cell->getStyle()->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Sample row
        $sample = [
            'John Doe', 'Engineering', 'Software Engineer', 'EMP001',
            'O+ve', '9876543210', '9876543211', '9876543212',
            '2024-01-15', '1995-06-20', '', 'MyPass@123',
            'john.doe@company.com', 'MyPass@123', 'john.personal@gmail.com',
            '123 Main St, City', '456 Home St, Town',
        ];
        $sheet->fromArray([$sample], null, 'A2');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tmpFile = tempnam(sys_get_temp_dir(), 'emp_template_') . '.xlsx';
        $writer->save($tmpFile);

        return response()->download($tmpFile, 'employee_import_template.xlsx')->deleteFileAfterSend(true);
    }

    // ─────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────

    /**
     * Handle all file uploads and return updated $data array.
     */
    private function handleFileUploads(Request $request, array $data): array
    {
        $fileFields = [
            'avatar', 'aadhaar_card', 'pan_card',
            'matric_certificate', 'plus_two_certificate',
            'bachelor_degree_certificate', 'master_degree_certificate',
            'address_proof', 'last_company_release_letter',
            'last_company_offer_letter', 'salary_slip_1',
            'salary_slip_2', 'salary_slip_3', 'bank_passbook_page',
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('employees/docs', 'public');
            }
        }

        return $data;
    }

    /**
     * Parse date from Excel — handles both string dates and Excel serial numbers.
     */
    private function parseExcelDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Already a formatted date string
        if (preg_match('/\d{4}-\d{2}-\d{2}/', $value)) {
            return $value;
        }

        // Numeric Excel serial
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value)
                    ->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Try Carbon parse for other formats
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
    public function export(Request $request): StreamedResponse
{
    $query = Employee::with(['department', 'schedule']);

    if ($request->filled('department_id')) {
        $query->where('department_id', $request->department_id);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('firstname', 'like', "%{$search}%")
              ->orWhere('lastname', 'like', "%{$search}%")
              ->orWhere('unique_id', 'like', "%{$search}%");
        });
    }

    $employees = $query->latest()->get();

    $spreadsheet = new Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Employees');

    // Header row
    $headings = ['SL', 'Emp ID', 'Name of Employee', 'Email', 'Department', 'Schedule', 'Date Joined'];
    $sheet->fromArray($headings, null, 'A1');
    $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Data rows
    $row = 2;
    foreach ($employees as $index => $employee) {
        $sheet->fromArray([
            $index + 1,
            $employee->unique_id,
            trim($employee->firstname . ' ' . $employee->lastname),
            $employee->email ?? '-',
            $employee->department->title ?? '-',
            $employee->schedule->title ?? '-',
            $employee->created_at->format('Y-m-d'),
        ], null, "A{$row}");
        $row++;
    }

    // Auto-size all columns
    foreach (range('A', 'G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $filename = 'employees-' . now()->format('Y-m-d_His') . '.xlsx';

    $writer = new Xlsx($spreadsheet);

    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Cache-Control'       => 'max-age=0',
    ]);
}
public function toggleStatus(Request $request, Employee $employee)
{
    $request->validate([
        'emp_status' => 'required|in:active,inactive',
    ]);

    $employee->update([
        'emp_status' => $request->emp_status,
    ]);

    return response()->json([
        'success' => true,
        'status' => ucfirst($employee->emp_status),
        'message' => 'Employee status updated successfully.'
    ]);
}
}
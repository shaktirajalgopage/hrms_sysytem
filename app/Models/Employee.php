<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'department_id',
        'designation_id',
        'schedule_id',
        'firstname',
        'lastname',
        'unique_id',
        'email',
        'phone',
        'address',
        'dob',
        'gender',
        'religion',
        'marital',
        'status',
        'avatar',

        'aadhaar_card',
        'pan_card',
        'matric_certificate',
        'plus_two_certificate',
        'bachelor_degree_certificate',
        'master_degree_certificate',
        'address_proof',
        'last_company_release_letter',
        'last_company_offer_letter',
        'salary_slip_1',
        'salary_slip_2',
        'salary_slip_3',
        'bank_passbook_page',
        'emp_status',
        'official_email',
        'personal_email',
        'emergency_phone',
        'doj',
        'blood_group',
        'permanent_address',
        'present_address'
        
    ];
// Define relationships
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo {
        return $this->belongsTo(Designation::class);
    }

    public function attendances(): HasMany {
        return $this->hasMany(Attendance::class); // Replace 'Attendance' with your actual attendance model name
    }

    public function schedule(): BelongsTo {
        return $this->belongsTo(Schedule::class); // Replace 'Attendance' with your actual attendance model name
    }

    public function departs(): HasMany {
        return $this->hasMany(Depart::class);
    }

    public function check(): HasMany {
        return $this->hasMany(Check::class);
    }

    public function salary(): HasOne {
        return $this->hasOne(Salary::class);
    }

    public function leaves(): HasMany {
        return $this->hasMany(Leave::class);
    }

    public function allowances(): HasMany {
        return $this->hasMany(Allowance::class);
    }

    public function lateTime(): HasMany {
        return $this->hasMany(LateTime::class);
    }

    public function overTime(): HasMany {
        return $this->hasMany(OverTime::class);
    }

    public function payrolls(): HasMany {
        return $this->hasMany(Payroll::class);
    }

    public function hierarchy(): HasOne
{
    return $this->hasOne(EmployeeHierarchy::class);
}

public function teamLead(): HasOne
{
    return $this->hasOne(EmployeeHierarchy::class)->with('teamLead');
}

public function asset()
{
    return $this->hasOne(EmployeeAsset::class);
}

public function salarySlips(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(SalarySlip::class);
    }

public function assetRequests()
{
    return $this->hasMany(
        AssetRequest::class
    );
}

public function leaveAllocations()
{
    return $this->hasMany(EmployeeLeaveAllocation::class, 'employee_id');
}

// Helper — get the first approver when submitting a leave
public function getFirstApprover(): ?Employee
{
    $h = $this->hierarchy;
    if (!$h) return null;

    // First approver in chain: team lead → manager → hr
    return $h->teamLead ?? $h->manager ?? $h->hr ?? null;
}

}


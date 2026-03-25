<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmStaff extends Model
{
    public $table = 'hrmstaffs';

    protected $fillable = [
        // Personal details
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'date_of_birth',
        'gender',

        // Employment details
        'school_id',
        'staff_number',
        'shift_number',
        'designation',
        'department',
        'date_hired',
        'contract_end',

        // Payroll & statutory
        'kra_pin',
        'nssf_number',
        'nhif_number',
        'basic_salary',
        'allowances',
        'deductions',
        'bank_name',
        'bank_account_number',

        // System info
        'active',
    ];

    protected $casts = [
        'date_of_birth'   => 'date',
        'date_hired'      => 'date',
        'contract_end'    => 'date',
        'basic_salary'    => 'decimal:2',
        'allowances'      => 'decimal:2',
        'deductions'      => 'decimal:2',
        'active'          => 'boolean',
    ];

    public static array $rules = [
        'first_name'           => 'required|string|max:255',
        'last_name'            => 'required|string|max:255',
        'email'                => 'required|email|unique:hrmstaffs,email',
        'phone_number'         => 'nullable|string|max:20',
        'date_of_birth'        => 'nullable|date',
        'gender'               => 'nullable|string|max:20',

        'school_id'            => 'required|exists:schools,id',
        'staff_number'         => 'required|string|max:50|unique:hrmstaffs,staff_number',
        'shift_number'         => 'nullable|string|max:50',
        'designation'          => 'nullable|string|max:255',
        'department'           => 'nullable|string|max:255',
        'date_hired'           => 'nullable|date',
        'contract_end'         => 'nullable|date',

        'kra_pin'              => 'nullable|string|max:50',
        'nssf_number'          => 'nullable|string|max:50',
        'nhif_number'          => 'nullable|string|max:50',
        'basic_salary'         => 'nullable|numeric|min:0',
        'allowances'           => 'nullable|numeric|min:0',
        'deductions'           => 'nullable|numeric|min:0',
        'bank_name'            => 'nullable|string|max:255',
        'bank_account_number'  => 'nullable|string|max:100',

        'active'               => 'boolean',
    ];

    // Relationships
    public function school()
    {
        return $this->belongsTo(\App\Models\School::class, 'school_id');
    }
}

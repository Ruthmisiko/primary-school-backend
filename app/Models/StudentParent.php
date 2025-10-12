<?php

namespace App\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class StudentParent extends Model
{
    public $table = 'parents';

    public $fillable = [
        'name',
        'phone_number',
        'address',
        'students_id',
        'id_number',
        'gender',
        'student_id',
        'school_id'
    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }





}

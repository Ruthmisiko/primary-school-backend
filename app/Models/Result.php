<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public $table = 'results';

    protected $fillable = [
        'class_id',
        'student_id',
        'exam_id',
        'subject_id',
        'marks_obtained',
        'grade',
        'remarks',
    ];


    protected $casts = [

    ];

    public static array $rules = [

    ];


    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function sclass()
    {
        return $this->belongsTo(Sclass::class, 'class_id', 'id');
    }

}

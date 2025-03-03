<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    public $table = 'results';

    public $fillable = [
        'english',
        'class_id',
        'student_id',
        'kiswahili',
        'mathematics',
        'cre',
        'science'
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

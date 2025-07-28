<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    public $table = 'students';

    public $fillable = [
        'name',
        'class_id',
        'parent',
        'age',
        'fee_balance',
         'paid_fee'
    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];

    public function sclass()
    {
        return $this->belongsTo(Sclass::class, 'class_id', 'id');
    }


    public function results()
    {
        return $this->hasMany(Result::class, 'student_id', 'id');
    }

}

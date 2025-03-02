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
    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];


}

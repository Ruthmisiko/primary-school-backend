<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    public $table = 'teachers';

    public $fillable = [
        'name',
        'gender',
        'contact_number',
        'designation',
        'email',
        'assigned_class'
    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];

    public function sclass()
    {
        return $this->hasMany(Sclass::class, 'teacher_id');
    }
}

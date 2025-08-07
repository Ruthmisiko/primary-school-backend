<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'name',
        'address',
        'email',
        'phone',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'school_id', 'id');
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class, 'school_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'school_id', 'id');
    }

    public function sclasses()
    {
        return $this->hasMany(Sclass::class, 'school_id', 'id');
    }

    public function subjects()
    {
        return $this->hasMany(subject::class, 'school_id', 'id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'school_id', 'id');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'school_id', 'id');
    }

    public function settings()
    {
        return $this->hasMany(Setting::class, 'school_id', 'id');
    }
}

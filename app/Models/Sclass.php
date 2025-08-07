<?php

namespace App\Models;

use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Model;

class Sclass extends Model
{
    public $table = 'sclasses';

    public $fillable = [
        'name',
        'teacher_id',
        'fee',
        'school_id'
    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];
    public function results()
    {
        return $this->hasMany(Result::class, 'class_id', 'id');
    }

    public function teacher()
{
    return $this->belongsTo(Teacher::class);
}

    public function students()
{
    return $this->hasMany(Student::class, 'class_id', 'id');
}
public function school()
{
    return $this->belongsTo(School::class, 'school_id', 'id');
}

protected static function booted()
{
    static::addGlobalScope(new SchoolScope);
}

}

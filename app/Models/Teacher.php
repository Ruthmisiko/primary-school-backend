<?php

namespace App\Models;

use App\Models\Scopes\SchoolScope;
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
        'assigned_class',
        'subjects',
        'school_id'
    ];

    protected $casts = [
        'subjects' => 'array',
    ];

    public static array $rules = [

    ];
    public function sclass()
    {
        return $this->belongsTo(Sclass::class, 'assigned_class', 'id');
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

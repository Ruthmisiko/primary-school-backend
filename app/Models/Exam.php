<?php

namespace App\Models;

use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    public $table = 'exams';

    public $fillable = [
        'year',
        'name',
        'term',
        'class_id',
        'school_id'
    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];

    public function sclass()
    {
        return $this->belongsTo(Sclass::class, 'class_id', 'id');
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

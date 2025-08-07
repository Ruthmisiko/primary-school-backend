<?php

namespace App\Models;

use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Model;

class subject extends Model
{
    public $table = 'subjects';

    public $fillable = [
        'name',
        'code',
        'description',
        'school_id'
    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    protected static function booted()
{
    static::addGlobalScope(new SchoolScope);
}
}

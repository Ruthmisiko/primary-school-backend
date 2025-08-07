<?php

namespace App\Models;

use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $table = 'settings';

    public $fillable = [
        'business_name',
        'business_email',
        'business_phone',
        'user_id',
        'location',
        'school_id'
    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
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

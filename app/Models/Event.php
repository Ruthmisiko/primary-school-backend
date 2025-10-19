<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public $table = 'events';

    public $fillable = [
        'school_id',
        'tittle',
        'description',
        'date',
        'status',
    ];

    protected $casts = [
        
    ];

    public static array $rules = [
        
    ];

    
}

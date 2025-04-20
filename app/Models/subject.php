<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class subject extends Model
{
    public $table = 'subjects';

    public $fillable = [
        'name',
        'code',
        'description',
    ];

    protected $casts = [
        
    ];

    public static array $rules = [
        
    ];

    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $table = 'settings';

    public $fillable = [
        'business_name',
        'business_email',
        'business_phone'
    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];


}

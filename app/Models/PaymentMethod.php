<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    public $table = 'payment_methods';

    public $fillable = [
        'name',
        'description',
        'school_id'
    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];


}

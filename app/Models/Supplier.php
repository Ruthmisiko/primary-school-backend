<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    public $table = 'suppliers';

    public $fillable = [
        'name',
        'category',
        'contact_person',
        'email',
        'phone',
        'address',
        'description',
        'school_id'

    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];


}

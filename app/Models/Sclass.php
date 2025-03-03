<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sclass extends Model
{
    public $table = 'sclasses';

    public $fillable = [
        'name',
    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];
    public function results()
    {
        return $this->hasMany(Result::class, 'class_id', 'id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    public $table = 'exams';

    public $fillable = [
        'year',
        'name',
        'term',
        'class_id'
    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];

    public function sclass()
    {
        return $this->belongsTo(Sclass::class, 'class_id', 'id');
    }

}

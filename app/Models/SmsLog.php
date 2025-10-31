<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'phone_number',
        'message',
        'status',
        'response_json',
        'sent_at',
        'school_id',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(StudentParent::class, 'parent_id', 'id');
    }
}



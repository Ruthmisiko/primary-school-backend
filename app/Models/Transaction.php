<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'payment_id',
        'pesapal_merchant_reference',
        'pesapal_tracking_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship with Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship with Payment
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}

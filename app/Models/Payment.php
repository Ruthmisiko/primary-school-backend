<?php

namespace App\Models;

use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    // Fields that can be mass-assigned
    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'payment_method',
        'transaction_id',
        'school_id',
        'status',
        'description',
        'callback_data',
        'student_id'
    ];

    // Casts (automatically convert data types)
    protected $casts = [
        'amount' => 'decimal:2',
        'callback_data' => 'array',
    ];

    public static array $rules = [

    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    public $table = 'expenses';

    protected $fillable = [
        'school_id',
        'category',
        'description',
        'amount',
        'currency',
        'payment_method',
        'expense_date',
        'receipt_number',
        'status',
    ];

    protected $casts = [

    ];

    public static array $rules = [

    ];


}

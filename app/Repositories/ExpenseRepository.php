<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Repositories\BaseRepository;

class ExpenseRepository extends BaseRepository
{
    protected $fieldSearchable = [
        
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Expense::class;
    }
}

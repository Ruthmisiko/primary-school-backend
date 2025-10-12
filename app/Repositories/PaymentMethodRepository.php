<?php

namespace App\Repositories;

use App\Models\PaymentMethod;
use App\Repositories\BaseRepository;

class PaymentMethodRepository extends BaseRepository
{
    protected $fieldSearchable = [
        
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return PaymentMethod::class;
    }
}

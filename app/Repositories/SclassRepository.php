<?php

namespace App\Repositories;

use App\Models\Sclass;
use App\Repositories\BaseRepository;

class SclassRepository extends BaseRepository
{
    protected $fieldSearchable = [
        
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Sclass::class;
    }
}

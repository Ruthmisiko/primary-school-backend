<?php

namespace App\Repositories;

use App\Models\Parent;
use App\Repositories\BaseRepository;

class ParentRepository extends BaseRepository
{
    protected $fieldSearchable = [
        
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Parent::class;
    }
}

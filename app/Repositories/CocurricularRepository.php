<?php

namespace App\Repositories;

use App\Models\Cocurricular;
use App\Repositories\BaseRepository;

class CocurricularRepository extends BaseRepository
{
    protected $fieldSearchable = [
        
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Cocurricular::class;
    }
}

<?php

namespace App\Repositories;

use App\Models\subject;
use App\Repositories\BaseRepository;

class subjectRepository extends BaseRepository
{
    protected $fieldSearchable = [
        
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return subject::class;
    }
}

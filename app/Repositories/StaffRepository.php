<?php

namespace App\Repositories;

use App\Models\Staff;
use App\Repositories\BaseRepository;

class StaffRepository extends BaseRepository
{
    protected $fieldSearchable = [
        
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Staff::class;
    }
}

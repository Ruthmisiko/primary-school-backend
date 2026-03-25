<?php

namespace App\Repositories;

use App\Models\HrmStaff;
use App\Repositories\BaseRepository;

class HrmStaffRepository extends BaseRepository
{
    protected $fieldSearchable = [
        
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrmStaff::class;
    }
}

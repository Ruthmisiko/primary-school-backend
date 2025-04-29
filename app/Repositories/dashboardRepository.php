<?php

namespace App\Repositories;

use App\Models\dashboard;
use App\Repositories\BaseRepository;

class dashboardRepository extends BaseRepository
{
    protected $fieldSearchable = [
        
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return dashboard::class;
    }
}

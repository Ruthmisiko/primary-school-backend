<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\BaseRepository;

class StudentRepository extends BaseRepository
{
    protected $fieldSearchable = [
        // Add searchable fields if needed, like 'name', 'class_id', etc.
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Student::class;
    }

    public function all(array $search = [], int $skip = null, int $limit = null, array $columns = ['*']): \Illuminate\Database\Eloquent\Collection
{
    $query = Student::where('school_id', auth()->user()->school_id);

    // Optional: Handle search fields if needed
    foreach ($search as $key => $value) {
        if (in_array($key, $this->getFieldsSearchable())) {
            $query->where($key, $value);
        }
    }

    if (!is_null($skip)) {
        $query->skip($skip);
    }

    if (!is_null($limit)) {
        $query->limit($limit);
    }

    return $query->get($columns);
}


    // Override find() to ensure a school cannot fetch another school's student
    public function find($id, $columns = ['*'])
    {
        return Student::where('id', $id)
                      ->where('school_id', auth()->user()->school_id)
                      ->first($columns);
    }

    public function paginate(int $perPage = 10, array $search = [], array $columns = ['*'], string $pageName = 'page', int|null $page = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Student::where('school_id', auth()->user()->school_id);

        // Optional: Handle search fields if needed
        foreach ($search as $key => $value) {
            if (in_array($key, $this->getFieldsSearchable())) {
                $query->where($key, $value);
            }
        }

        return $query->paginate($perPage, $columns, $pageName, $page);
    }

}


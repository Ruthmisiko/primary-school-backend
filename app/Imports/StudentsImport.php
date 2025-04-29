<?php
namespace App\Imports;

use App\Models\Sclass;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class StudentsImport implements ToCollection
{
    protected $currentStudents = null;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header row

            try {
                // Extract all data elements
                $name  = $row[0] ?? null;
                $parent   = $row[1] ?? null;
                $class       = $row[2] ?? null;
                $age    = $row[3] ?? null;
                $fee_balance   = $row[4] ?? null;
                $paid_fee   = $row[5] ?? null;


                $this->processStudentData(
                    $index,
                    $name,
                    $parent,
                    $class,
                    $age,
                    $fee_balance,
                    $paid_fee,
                );


            } catch (\Exception $e) {
                Log::error("Error processing row {$index}: " . $e->getMessage());
            }
        }
    }

    private function processStudentData($rowIndex, $name, $parent, $class, $age, $fee_balance, $paid_fee)
    {

        $class =Sclass::where('name', $name)->first();

        $this->currentStudents = Student::updateOrCreate(
            [
                'name' => $name
            ],
            [
                'class_id' => $class ? $class->id : null,
                'parent' => $parent,
                'age' => $age,
                'fee_balance' => $fee_balance,
                'paid_fee' => $paid_fee,
            ]
        );

    }


}
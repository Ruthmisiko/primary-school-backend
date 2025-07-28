<?php

namespace App\Imports;

use App\Models\Exam;
use App\Models\Result;
use App\Models\Sclass;
use App\Models\subject;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class ResultsImport implements ToCollection
{
    protected $examId;
    protected $classId;
    protected $subjectId;
    protected $processedCount = 0;
    protected $errorCount = 0;
    protected $metadataErrors = [];

    public function collection(Collection $rows)
    {
        // Process metadata (first 3 rows)
        if (!$this->processMetadata($rows)) {
            throw new \Exception(implode("\n", $this->metadataErrors));
        }

        // Process results (starting from row 6)
        foreach ($rows as $rowIndex => $row) {
            // Skip header rows (0-4) and empty rows
            if ($rowIndex < 5 || empty($row[0])) {
                continue;
            }

            try {
                $this->processResultRow(
                    trim($row[0]), // Student name
                    $row[1],       // Marks
                    $row[2] ?? '', // Remarks
                    $rowIndex + 1  // Excel row number
                );
                $this->processedCount++;
            } catch (\Exception $e) {
                Log::error("Row ".($rowIndex+1).": ".$e->getMessage());
                $this->errorCount++;
            }
        }
    }

    private function processMetadata(Collection $rows): bool
    {
        $this->metadataErrors = [];

        // Get values from Excel (rows are 0-indexed)
        $examName = trim($rows[0][1] ?? '');    // Row 1, Column B
        $className = trim($rows[1][1] ?? '');   // Row 2, Column B
        $subjectName = trim($rows[2][1] ?? ''); // Row 3, Column B

        // Validate Exam
        if (empty($examName)) {
            $this->metadataErrors[] = "Exam name is required in cell B1";
        } elseif (!$exam = Exam::where('name', $examName)->first()) {
            $this->metadataErrors[] = "Exam not found: '{$examName}'. Create it first or check spelling.";
        } else {
            $this->examId = $exam->id;
        }

        // Validate Class
        if (empty($className)) {
            $this->metadataErrors[] = "Class name is required in cell B2";
        } elseif (!$class = Sclass::where('name', $className)->first()) {
            $this->metadataErrors[] = "Class not found: '{$className}'. Create it first or check spelling.";
        } else {
            $this->classId = $class->id;
        }

        // Validate Subject
        if (empty($subjectName)) {
            $this->metadataErrors[] = "Subject name is required in cell B3";
        } elseif (!$subject = subject::where('name', $subjectName)->first()) {
            $this->metadataErrors[] = "Subject not found: '{$subjectName}'. Create it first or check spelling.";
        } else {
            $this->subjectId = $subject->id;
        }

        return empty($this->metadataErrors);
    }

    private function processResultRow($studentName, $marks, $remarks, $excelRowNumber)
    {
        // Validate student exists
        $student = Student::where('name', trim($studentName))->first();
        if (!$student) {
            throw new \Exception("Student not found: {$studentName}");
        }

        // Validate marks
        if (!is_numeric($marks)) {
            throw new \Exception("Marks must be a number (value: {$marks})");
        }

        // Create/update result
        Result::updateOrCreate(
            [
                'exam_id' => $this->examId,
                'class_id' => $this->classId,
                'subject_id' => $this->subjectId,
                'student_id' => $student->id
            ],
            [
                'marks_obtained' => $marks,
                'remarks' => $remarks,
                // Add other fields as needed
            ]
        );
    }

    public function getProcessedCount(): int
    {
        return $this->processedCount;
    }

    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    public function getMetadataErrors(): array
    {
        return $this->metadataErrors;
    }
}
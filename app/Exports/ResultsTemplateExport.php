<?php

namespace App\Exports;

use App\Models\Exam;
use App\Models\Sclass;
use App\Models\subject;
use App\Models\Student;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ResultsTemplateExport implements FromCollection, WithHeadings, WithEvents
{
    public function collection()
    {
        // Return empty rows (e.g., 100) to pre-fill the dropdowns for Student
        return collect(array_fill(0, 100, ['Student' => '', 'Marks' => '', 'Remarks' => '']));
    }



    public function headings(): array
    {
        return [

        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Add labels for top dropdowns
                $sheet->setCellValue('A1', 'Exam');
                $sheet->setCellValue('A2', 'Class');
                $sheet->setCellValue('A3', 'Subject');

                $sheet->setCellValue('A5', 'STUDENT');
                $sheet->setCellValue('B5', 'MARKS');
                $sheet->setCellValue('C5', 'REMARKS');
                // Optional styling
                $sheet->getStyle('A1:A3')->getFont()->setBold(true);

                // Fetch dropdown data
                $exams = array_slice(Exam::pluck('name')->toArray(), 0, 100);
                $classes = array_slice(Sclass::pluck('name')->toArray(), 0, 100);
                $subjects = array_slice(subject::pluck('name')->toArray(), 0, 100);
                $students = array_slice(Student::pluck('name')->toArray(), 0, 500);

                // Set dropdowns for top section
                $this->setDropdown($sheet, 'B1', $exams);
                $this->setDropdown($sheet, 'B2', $classes);
                $this->setDropdown($sheet, 'B3', $subjects);

                // Set dropdown for Student column starting A6 to A105
                $this->setDropdown($sheet, 'A6:A105', $students);

                // Style the headers
                $sheet->getStyle('A5:C5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4']
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
            },
        ];
    }

    private function setDropdown($sheet, $range, $options)
    {
        $validation = new DataValidation();
        $validation->setType(DataValidation::TYPE_LIST)
            ->setErrorStyle(DataValidation::STYLE_INFORMATION)
            ->setAllowBlank(false)
            ->setShowInputMessage(true)
            ->setShowErrorMessage(true)
            ->setShowDropDown(true)
            ->setFormula1('"' . implode(',', $options) . '"');

        // Handle ranges like A6:A105 or single cells like B1
        if (strpos($range, ':') !== false) {
            [$startCell, $endCell] = explode(':', $range);
            $startCol = preg_replace('/[0-9]/', '', $startCell);
            $startRow = (int) preg_replace('/[^0-9]/', '', $startCell);
            $endRow = (int) preg_replace('/[^0-9]/', '', $endCell);

            for ($row = $startRow; $row <= $endRow; $row++) {
                $cell = $startCol . $row;
                $sheet->getCell($cell)->setDataValidation(clone $validation);
            }
        } else {
            $sheet->getCell($range)->setDataValidation($validation);
        }
    }
}

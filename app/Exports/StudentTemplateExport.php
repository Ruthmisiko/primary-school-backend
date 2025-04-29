<?php

namespace App\Exports;

use App\Models\Sclass;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StudentTemplateExport implements FromCollection, WithHeadings, WithEvents
{
    public function collection()
    {
        // Return an empty collection to generate a blank template
        return collect([]);
    }

    public function headings(): array
{
    return [
        'Name',
        'Parent',
        'Class',
        'Age',
        'Fee Balance',
        'Paid Fee',
    ];
}

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Styling headers
                $sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);

                  // Fetch dropdown data from database

                $classes = array_slice(Sclass::pluck('name')->toArray(), 0, 100);

                $this->setDropdown($sheet, 'C2:H2000', $classes);

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

        // Apply validation to each cell in the given range
        preg_match('/([A-Z]+)([0-9]+):([A-Z]+)([0-9]+)/', $range, $matches);
        if ($matches) {
            $startColumn = $matches[1]; // e.g., "A"
            $startRow = (int) $matches[2]; // e.g., 2
            $endColumn = $matches[3]; // e.g., "A"
            $endRow = (int) $matches[4]; // e.g., 2000

            for ($row = $startRow; $row <= $endRow; $row++) {
                $cellCoordinate = $startColumn . $row;
                $sheet->getCell($cellCoordinate)->setDataValidation(clone $validation);
            }
        }
    }

}


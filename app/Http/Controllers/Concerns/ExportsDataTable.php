<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

trait ExportsDataTable
{
    protected function applyDateFilters($query, Request $request, string $column = 'created_at')
    {
        if ($request->filled('start_date')) {
            $query->whereDate($column, '>=', $request->date('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate($column, '<=', $request->date('end_date'));
        }
        return $query;
    }

    protected function exportWithImages($rows, array $headings, ?string $imageKey = null, string $fileNamePrefix = 'export')
    {
        $export = new class($rows, $headings, $imageKey) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithEvents {
            private $rows; private $headings; private $imageKey;
            
            public function __construct($rows, $headings, $imageKey) { 
                $this->rows = $rows; 
                $this->headings = $headings; 
                $this->imageKey = $imageKey; 
            }
            
            public function array(): array {
                $out = [];
                foreach ($this->rows as $row) {
                    $mapped = [];
                    foreach ($this->headings as $key => $label) {
                        if ($this->imageKey && $key === $this->imageKey) {
                            // For image columns, show empty string instead of path
                            $mapped[$label] = '';
                        } else {
                            $value = is_array($row) ? ($row[$key] ?? null) : (is_object($row) ? ($row->{$key} ?? null) : null);
                            $mapped[$label] = $value;
                        }
                    }
                    $out[] = $mapped;
                }
                return $out;
            }
            
            public function headings(): array { 
                return array_values($this->headings); 
            }
            
            public function registerEvents(): array {
                return [
                    \Maatwebsite\Excel\Events\AfterSheet::class => function(\Maatwebsite\Excel\Events\AfterSheet $event) {
                        $sheet = $event->sheet->getDelegate();
                        $rowIndex = 2;
                        
                        if ($this->imageKey) {
                            $imageColIndex = array_search($this->imageKey, array_keys($this->headings), true);
                            if ($imageColIndex !== false) {
                                // Convert index to Excel column (0->A, 1->B ...)
                                $colLetter = chr(ord('A') + $imageColIndex);
                                foreach ($this->rows as $row) {
                                    $path = is_array($row) ? ($row[$this->imageKey] ?? null) : (is_object($row) ? ($row->{$this->imageKey} ?? null) : null);
                                    if ($path && is_file($path)) {
                                        $drawing = new Drawing();
                                        $drawing->setPath($path);
                                        $drawing->setHeight(48);
                                        $drawing->setCoordinates($colLetter . $rowIndex);
                                        $drawing->setWorksheet($sheet);
                                        $sheet->getRowDimension($rowIndex)->setRowHeight(38);
                                    }
                                    $rowIndex++;
                                }
                                $sheet->getColumnDimension($colLetter)->setWidth(12);
                            }
                        }
                        
                        // Auto-size columns and make header bold
                        $lastCol = chr(ord('A') + max(0, count($this->headings) - 1));
                        foreach (range('A', $lastCol) as $letter) { 
                            $sheet->getColumnDimension($letter)->setAutoSize(true); 
                        }
                        $event->sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);
                    }
                ];
            }
        };
        
        $fileName = $fileNamePrefix . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download($export, $fileName);
    }
}
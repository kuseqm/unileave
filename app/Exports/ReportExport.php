<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $definition;

    public function __construct($data, $definition)
    {
        $this->data = $data;
        $this->definition = $definition;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        // Use the column names from the first row of data, or fallback
        if ($this->data->isNotEmpty()) {
            return array_keys($this->data->first());
        }
        return [];
    }
}
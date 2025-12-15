<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class GenericExport implements FromCollection, WithHeadings, WithTitle
{
    protected $data;
    protected $title;

    public function __construct($data, $title = 'Report')
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        if (empty($this->data)) {
            return [];
        }
        return array_keys($this->data[0]);
    }

    public function title(): string
    {
        return $this->title;
    }
}


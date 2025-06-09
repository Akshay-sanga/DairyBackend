<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MilkRatesExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'fat','snf_7_5','snf_7_6','snf_7_7','snf_7_8','snf_7_9','snf_8_0','snf_8_1','snf_8_2', 'snf_8_3', 'snf_8_4', 'snf_8_5', 'snf_8_6',
            'snf_8_7', 'snf_8_8', 'snf_8_9', 'snf_9_0',
        ];
    }
} 
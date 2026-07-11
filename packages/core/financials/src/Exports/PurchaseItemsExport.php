<?php

namespace Core\Financials\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Financials\Models\PurchaseItem;

class PurchaseItemsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
    public function __construct(protected $headersOnly = false, protected $cols = [])
    {
    }

    public function collection()
    {
        if ($this->headersOnly) {
            return collect([]);
        }
        return PurchaseItem::get();
    }

    public function headings(): array
    {
        $headings = [];

        if (empty($this->cols) || in_array('id', $this->cols)) {
            $headings[] = trans('id');
        }
        if (empty($this->cols) || in_array('name', $this->cols)) {
            $headings[] = trans('name');
        }

        return $headings;
    }

    public function map($model): array
    {
        $data = [];

        if (empty($this->cols) || in_array('id', $this->cols)) {
            $data[] = $model->id;
        }
        if (empty($this->cols) || in_array('name', $this->cols)) {
            $data[] = $model->name;
        }

        return $data;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => "\n",
        ];
    }
}

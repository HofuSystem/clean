<?php

namespace Core\Financials\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Financials\Models\Purchase;

class PurchasesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
    public function __construct(protected $headersOnly = false, protected $cols = [])
    {
    }

    public function collection()
    {
        if ($this->headersOnly) {
            return collect([]);
        }
        return Purchase::get();
    }

    public function headings(): array
    {
        $headings = [];

        if (empty($this->cols) || in_array('id', $this->cols)) {
            $headings[] = trans('id');
        }
        if (empty($this->cols) || in_array('item_id', $this->cols)) {
            $headings[] = trans('item');
        }
        if (empty($this->cols) || in_array('provider_id', $this->cols)) {
            $headings[] = trans('provider');
        }
        if (empty($this->cols) || in_array('value_before_tax', $this->cols)) {
            $headings[] = trans('value before tax');
        }
        if (empty($this->cols) || in_array('tax_value', $this->cols)) {
            $headings[] = trans('tax value');
        }
        if (empty($this->cols) || in_array('value_after_tax', $this->cols)) {
            $headings[] = trans('value after tax');
        }
        if (empty($this->cols) || in_array('notes', $this->cols)) {
            $headings[] = trans('notes');
        }
        if (empty($this->cols) || in_array('attachment', $this->cols)) {
            $headings[] = trans('attachment');
        }
        if (empty($this->cols) || in_array('collection_date', $this->cols)) {
            $headings[] = trans('collection date');
        }

        return $headings;
    }

    public function map($model): array
    {
        $data = [];

        if (empty($this->cols) || in_array('id', $this->cols)) {
            $data[] = $model->id;
        }
        if (empty($this->cols) || in_array('item_id', $this->cols)) {
            $data[] = $model->item_id;
        }
        if (empty($this->cols) || in_array('provider_id', $this->cols)) {
            $data[] = $model->provider_id;
        }
        if (empty($this->cols) || in_array('value_before_tax', $this->cols)) {
            $data[] = $model->value_before_tax;
        }
        if (empty($this->cols) || in_array('tax_value', $this->cols)) {
            $data[] = $model->tax_value;
        }
        if (empty($this->cols) || in_array('value_after_tax', $this->cols)) {
            $data[] = $model->value_after_tax;
        }
        if (empty($this->cols) || in_array('notes', $this->cols)) {
            $data[] = $model->notes;
        }
        if (empty($this->cols) || in_array('attachment', $this->cols)) {
            $data[] = $model->attachment;
        }
        if (empty($this->cols) || in_array('collection_date', $this->cols)) {
            $data[] = $model->collection_date?->format('Y-m-d');
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

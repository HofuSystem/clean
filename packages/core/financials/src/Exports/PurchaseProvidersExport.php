<?php

namespace Core\Financials\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Financials\Models\PurchaseProvider;

class PurchaseProvidersExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
    public function __construct(protected $headersOnly = false, protected $cols = [])
    {
    }

    public function collection()
    {
        if ($this->headersOnly) {
            return collect([]);
        }
        return PurchaseProvider::get();
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
        if (empty($this->cols) || in_array('commercial_registration', $this->cols)) {
            $headings[] = trans('commercial registration');
        }
        if (empty($this->cols) || in_array('tax_number', $this->cols)) {
            $headings[] = trans('tax number');
        }
        if (empty($this->cols) || in_array('street_name', $this->cols)) {
            $headings[] = trans('street name');
        }
        if (empty($this->cols) || in_array('building_no', $this->cols)) {
            $headings[] = trans('building no');
        }
        if (empty($this->cols) || in_array('city_id', $this->cols)) {
            $headings[] = trans('city');
        }
        if (empty($this->cols) || in_array('district_id', $this->cols)) {
            $headings[] = trans('district');
        }
        if (empty($this->cols) || in_array('postal_code', $this->cols)) {
            $headings[] = trans('postal code');
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
        if (empty($this->cols) || in_array('commercial_registration', $this->cols)) {
            $data[] = $model->commercial_registration;
        }
        if (empty($this->cols) || in_array('tax_number', $this->cols)) {
            $data[] = $model->tax_number;
        }
        if (empty($this->cols) || in_array('street_name', $this->cols)) {
            $data[] = $model->street_name;
        }
        if (empty($this->cols) || in_array('building_no', $this->cols)) {
            $data[] = $model->building_no;
        }
        if (empty($this->cols) || in_array('city_id', $this->cols)) {
            $data[] = $model->city_id;
        }
        if (empty($this->cols) || in_array('district_id', $this->cols)) {
            $data[] = $model->district_id;
        }
        if (empty($this->cols) || in_array('postal_code', $this->cols)) {
            $data[] = $model->postal_code;
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

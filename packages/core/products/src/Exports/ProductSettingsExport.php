<?php

namespace Core\Products\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Products\Models\ProductSetting;

class ProductSettingsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
    public function __construct(protected $headersOnly = false, protected $cols = [])
    {
    }

    public function collection()
    {
        if ($this->headersOnly) {
            return collect([]);
        }

        return ProductSetting::get();
    }

    public function headings(): array
    {
        $headings = [];

        if(empty($this->cols) or in_array('id', $this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('slug', $this->cols)){
            $headings[] = trans('slug');
        }
        if(empty($this->cols) or in_array('name', $this->cols)){
            $headings[] = trans('name').'(en) translations.en.name';
        }
        if(empty($this->cols) or in_array('name', $this->cols)){
            $headings[] = trans('name').'(ar) translations.ar.name';
        }
        if(empty($this->cols) or in_array('product_id', $this->cols)){
            $headings[] = trans('product');
        }
        if(empty($this->cols) or in_array('addon_price', $this->cols)){
            $headings[] = trans('addon price');
        }
        if(empty($this->cols) or in_array('parent_id', $this->cols)){
            $headings[] = trans('parent');
        }
        if(empty($this->cols) or in_array('status', $this->cols)){
            $headings[] = trans('status');
        }             
        return $headings;
    }

    public function map($model): array
    {
        $data = [];
        
        if(empty($this->cols) or in_array('id', $this->cols)){
            $data[] = $model->id;
        }
        if(empty($this->cols) or in_array('slug', $this->cols)){
            $data[] = $model->slug;
        }
        if(empty($this->cols) or in_array('name', $this->cols)){
            $data[] = $model->translate('en')?->name;
        }
        if(empty($this->cols) or in_array('name', $this->cols)){
            $data[] = $model->translate('ar')?->name;
        }
        if(empty($this->cols) or in_array('product_id', $this->cols)){
            $data[] = $model->product_id;
        }
        if(empty($this->cols) or in_array('addon_price', $this->cols)){
            $data[] = $model->addon_price;
        }
        if(empty($this->cols) or in_array('parent_id', $this->cols)){
            $data[] = $model->parent_id;
        }
        if(empty($this->cols) or in_array('status', $this->cols)){
            $data[] = $model->status;
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

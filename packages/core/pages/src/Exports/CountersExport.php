<?php

namespace Core\Pages\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Pages\Models\Counter;


class CountersExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{

    public function __construct(protected $headersOnly = false,protected $cols = [])
    {
    }

    public function collection()
    {
        if ($this->headersOnly) {
            // Return an empty collection if only headers are needed
            return collect([]);
        }

        // Fetch coupon data from the database
        return Counter::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $headings[] = trans('title').'(en) translations.en.title';
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $headings[] = trans('title').'(ar) translations.ar.title';
        }
        if(empty($this->cols) or in_array('count',$this->cols)){
            $headings[] = trans('count');
        }
        if(empty($this->cols) or in_array('is_active',$this->cols)){
            $headings[] = trans('is active');
        }
        return $headings;
    }

    public function map($model): array
    {
        // Format the data before exporting
        $data = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $data[] = $model->id;
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $data[] = $model->translate('en')->title;
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $data[] = $model->translate('ar')->title;
        }
        if(empty($this->cols) or in_array('count',$this->cols)){
            $data[] = $model->count;
        }
        if(empty($this->cols) or in_array('is_active',$this->cols)){
            $data[] = $model->is_active;
        }
        return $data;
    }

    public function getCsvSettings(): array
    {
        // Optional: Customize CSV settings (e.g., delimiter)
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => "\n",
        ];
    }
}

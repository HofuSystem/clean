<?php

namespace Core\Pages\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Pages\Models\Comparison;


class ComparisonsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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

        // Fetch comparisons data from the database
        return Comparison::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('point',$this->cols)){
            $headings[] = trans('point').'(en) translations.en.point';
        }
        if(empty($this->cols) or in_array('point',$this->cols)){
            $headings[] = trans('point').'(ar) translations.ar.point';
        }
        if(empty($this->cols) or in_array('us_text',$this->cols)){
            $headings[] = trans('us text').'(en) translations.en.us_text';
        }
        if(empty($this->cols) or in_array('us_text',$this->cols)){
            $headings[] = trans('us text').'(ar) translations.ar.us_text';
        }
        if(empty($this->cols) or in_array('them_text',$this->cols)){
            $headings[] = trans('them text').'(en) translations.en.them_text';
        }
        if(empty($this->cols) or in_array('them_text',$this->cols)){
            $headings[] = trans('them text').'(ar) translations.ar.them_text';
        }
        if(empty($this->cols) or in_array('order',$this->cols)){
            $headings[] = trans('order');
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
        if(empty($this->cols) or in_array('point',$this->cols)){
            $data[] = $model->translate('en')->point;
        }
        if(empty($this->cols) or in_array('point',$this->cols)){
            $data[] = $model->translate('ar')->point;
        }
        if(empty($this->cols) or in_array('us_text',$this->cols)){
            $data[] = $model->translate('en')->us_text;
        }
        if(empty($this->cols) or in_array('us_text',$this->cols)){
            $data[] = $model->translate('ar')->us_text;
        }
        if(empty($this->cols) or in_array('them_text',$this->cols)){
            $data[] = $model->translate('en')->them_text;
        }
        if(empty($this->cols) or in_array('them_text',$this->cols)){
            $data[] = $model->translate('ar')->them_text;
        }
        if(empty($this->cols) or in_array('order',$this->cols)){
            $data[] = $model->order;
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


<?php

namespace Core\Info\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Info\Models\Nationality;


class NationalitiesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Nationality::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $headings[] = trans('name').'(en) translations.en.name';
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $headings[] = trans('name').'(ar) translations.ar.name';
        }
        if(empty($this->cols) or in_array('arranging',$this->cols)){
            $headings[] = trans('arranging');
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
        if(empty($this->cols) or in_array('name',$this->cols)){
            $data[] = $model->translate('en')->name;
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $data[] = $model->translate('ar')->name;
        }
        if(empty($this->cols) or in_array('arranging',$this->cols)){
            $data[] = $model->arranging;
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

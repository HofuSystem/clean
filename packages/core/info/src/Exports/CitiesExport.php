<?php

namespace Core\Info\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Info\Models\City;


class CitiesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return City::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('slug',$this->cols)){
            $headings[] = trans('slug');
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $headings[] = trans('name').'(en) translations.en.name';
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $headings[] = trans('name').'(ar) translations.ar.name';
        }
        if(empty($this->cols) or in_array('lat',$this->cols)){
            $headings[] = trans('lat');
        }
        if(empty($this->cols) or in_array('lng',$this->cols)){
            $headings[] = trans('lng');
        }
        if(empty($this->cols) or in_array('postal_code',$this->cols)){
            $headings[] = trans('postal code');
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $headings[] = trans('image');
        }
        if(empty($this->cols) or in_array('delivery_price',$this->cols)){
            $headings[] = trans('delivery price');
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $headings[] = trans('status');
        }
        if(empty($this->cols) or in_array('country_id',$this->cols)){
            $headings[] = trans('country');
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
        if(empty($this->cols) or in_array('slug',$this->cols)){
            $data[] = $model->slug;
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $data[] = $model->translate('en')->name;
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $data[] = $model->translate('ar')->name;
        }
        if(empty($this->cols) or in_array('lat',$this->cols)){
            $data[] = $model->lat;
        }
        if(empty($this->cols) or in_array('lng',$this->cols)){
            $data[] = $model->lng;
        }
        if(empty($this->cols) or in_array('postal_code',$this->cols)){
            $data[] = $model->postal_code;
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $data[] = $model->image;
        }
        if(empty($this->cols) or in_array('delivery_price',$this->cols)){
            $data[] = $model->delivery_price;
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $data[] = $model->status;
        }
        if(empty($this->cols) or in_array('country_id',$this->cols)){
            $data[] = $model->country_id;
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

<?php

namespace Core\Users\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Users\Models\Address;


class AddressesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Address::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('location',$this->cols)){
            $headings[] = trans('location');
        }
        if(empty($this->cols) or in_array('lat',$this->cols)){
            $headings[] = trans('lat');
        }
        if(empty($this->cols) or in_array('lng',$this->cols)){
            $headings[] = trans('lng');
        }
        if(empty($this->cols) or in_array('city_id',$this->cols)){
            $headings[] = trans('city');
        }
        if(empty($this->cols) or in_array('district_id',$this->cols)){
            $headings[] = trans('district');
        }
        if(empty($this->cols) or in_array('is_default',$this->cols)){
            $headings[] = trans('is_default');
        }
        if(empty($this->cols) or in_array('user_id',$this->cols)){
            $headings[] = trans('user');
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
        if(empty($this->cols) or in_array('location',$this->cols)){
            $data[] = $model->location;
        }
        if(empty($this->cols) or in_array('lat',$this->cols)){
            $data[] = $model->lat;
        }
        if(empty($this->cols) or in_array('lng',$this->cols)){
            $data[] = $model->lng;
        }
        if(empty($this->cols) or in_array('city_id',$this->cols)){
            $data[] = $model->city_id;
        }
        if(empty($this->cols) or in_array('district_id',$this->cols)){
            $data[] = $model->district_id;
        }
        if(empty($this->cols) or in_array('user_id',$this->cols)){
            $data[] = $model->user_id;
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

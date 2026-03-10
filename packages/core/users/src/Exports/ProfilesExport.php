<?php

namespace Core\Users\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Users\Models\Profile;


class ProfilesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Profile::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('country_id',$this->cols)){
            $headings[] = trans('country');
        }
        if(empty($this->cols) or in_array('city_id',$this->cols)){
            $headings[] = trans('city');
        }
        if(empty($this->cols) or in_array('district_id',$this->cols)){
            $headings[] = trans('district');
        }
        if(empty($this->cols) or in_array('other_city_name',$this->cols)){
            $headings[] = trans('other city name');
        }
        if(empty($this->cols) or in_array('user_id',$this->cols)){
            $headings[] = trans('user');
        }
        if(empty($this->cols) or in_array('bio',$this->cols)){
            $headings[] = trans('bio');
        }
        if(empty($this->cols) or in_array('lat',$this->cols)){
            $headings[] = trans('lat');
        }
        if(empty($this->cols) or in_array('lng',$this->cols)){
            $headings[] = trans('lng');
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
        if(empty($this->cols) or in_array('country_id',$this->cols)){
            $data[] = $model->country_id;
        }
        if(empty($this->cols) or in_array('city_id',$this->cols)){
            $data[] = $model->city_id;
        }
        if(empty($this->cols) or in_array('district_id',$this->cols)){
            $data[] = $model->district_id;
        }
        if(empty($this->cols) or in_array('other_city_name',$this->cols)){
            $data[] = $model->other_city_name;
        }
        if(empty($this->cols) or in_array('user_id',$this->cols)){
            $data[] = $model->user_id;
        }
        if(empty($this->cols) or in_array('bio',$this->cols)){
            $data[] = $model->bio;
        }
        if(empty($this->cols) or in_array('lat',$this->cols)){
            $data[] = $model->lat;
        }
        if(empty($this->cols) or in_array('lng',$this->cols)){
            $data[] = $model->lng;
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

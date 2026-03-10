<?php

namespace Core\Workers\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Workers\Models\Worker;


class WorkersExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Worker::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $headings[] = trans('image');
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $headings[] = trans('name');
        }
        if(empty($this->cols) or in_array('phone',$this->cols)){
            $headings[] = trans('phone');
        }
        if(empty($this->cols) or in_array('email',$this->cols)){
            $headings[] = trans('email');
        }
        if(empty($this->cols) or in_array('years_experience',$this->cols)){
            $headings[] = trans('years experience');
        }
        if(empty($this->cols) or in_array('address',$this->cols)){
            $headings[] = trans('address');
        }
        if(empty($this->cols) or in_array('birth_date',$this->cols)){
            $headings[] = trans('birth date');
        }
        if(empty($this->cols) or in_array('hour_price',$this->cols)){
            $headings[] = trans('hour price');
        }
        if(empty($this->cols) or in_array('gender',$this->cols)){
            $headings[] = trans('gender');
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $headings[] = trans('status');
        }
        if(empty($this->cols) or in_array('identity',$this->cols)){
            $headings[] = trans('identity');
        }
        if(empty($this->cols) or in_array('nationality_id',$this->cols)){
            $headings[] = trans('nationality');
        }
        if(empty($this->cols) or in_array('city_id',$this->cols)){
            $headings[] = trans('city');
        }
        if(empty($this->cols) or in_array('categories',$this->cols)){
            $headings[] = trans('categories');
        }
        if(empty($this->cols) or in_array('leaders',$this->cols)){
            $headings[] = trans('leaders');
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
        if(empty($this->cols) or in_array('image',$this->cols)){
            $data[] = $model->image;
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $data[] = $model->name;
        }
        if(empty($this->cols) or in_array('phone',$this->cols)){
            $data[] = $model->phone;
        }
        if(empty($this->cols) or in_array('email',$this->cols)){
            $data[] = $model->email;
        }
        if(empty($this->cols) or in_array('years_experience',$this->cols)){
            $data[] = $model->years_experience;
        }
        if(empty($this->cols) or in_array('address',$this->cols)){
            $data[] = $model->address;
        }
        if(empty($this->cols) or in_array('birth_date',$this->cols)){
            $data[] = $model->birth_date;
        }
        if(empty($this->cols) or in_array('hour_price',$this->cols)){
            $data[] = $model->hour_price;
        }
        if(empty($this->cols) or in_array('gender',$this->cols)){
            $data[] = $model->gender;
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $data[] = $model->status;
        }
        if(empty($this->cols) or in_array('identity',$this->cols)){
            $data[] = $model->identity;
        }
        if(empty($this->cols) or in_array('nationality_id',$this->cols)){
            $data[] = $model->nationality_id;
        }
        if(empty($this->cols) or in_array('city_id',$this->cols)){
            $data[] = $model->city_id;
        }
        if(empty($this->cols) or in_array('categories',$this->cols)){
            $data[] = $model->categories->pluck('id')->toArray();
        }
        if(empty($this->cols) or in_array('leaders',$this->cols)){
            $data[] = $model->leaders->pluck('id')->toArray();
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

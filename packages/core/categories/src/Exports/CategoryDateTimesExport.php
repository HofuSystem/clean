<?php

namespace Core\Categories\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Categories\Models\CategoryDateTime;


class CategoryDateTimesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return CategoryDateTime::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('type',$this->cols)){
            $headings[] = trans('type');
        }
        if(empty($this->cols) or in_array('category_id',$this->cols)){
            $headings[] = trans('category');
        }
        if(empty($this->cols) or in_array('city_id',$this->cols)){
            $headings[] = trans('city');
        }
        if(empty($this->cols) or in_array('date',$this->cols)){
            $headings[] = trans('date');
        }
        if(empty($this->cols) or in_array('from',$this->cols)){
            $headings[] = trans('from');
        }
        if(empty($this->cols) or in_array('to',$this->cols)){
            $headings[] = trans('to');
        }
        if(empty($this->cols) or in_array('order_count',$this->cols)){
            $headings[] = trans('order count');
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
        if(empty($this->cols) or in_array('type',$this->cols)){
            $data[] = $model->type;
        }
        if(empty($this->cols) or in_array('category_id',$this->cols)){
            $data[] = $model->category_id;
        }
        if(empty($this->cols) or in_array('city_id',$this->cols)){
            $data[] = $model->city_id;
        }
        if(empty($this->cols) or in_array('date',$this->cols)){
            $data[] = $model->date;
        }
        if(empty($this->cols) or in_array('from',$this->cols)){
            $data[] = $model->from;
        }
        if(empty($this->cols) or in_array('to',$this->cols)){
            $data[] = $model->to;
        }
        if(empty($this->cols) or in_array('order_count',$this->cols)){
            $data[] = $model->order_count;
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

<?php

namespace Core\Orders\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Orders\Models\OrderRepresentative;


class OrderRepresentativesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return OrderRepresentative::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('order_id',$this->cols)){
            $headings[] = trans('order');
        }
        if(empty($this->cols) or in_array('representative_id',$this->cols)){
            $headings[] = trans('representative');
        }
        if(empty($this->cols) or in_array('type',$this->cols)){
            $headings[] = trans('type');
        }
        if(empty($this->cols) or in_array('date',$this->cols)){
            $headings[] = trans('date');
        }
        if(empty($this->cols) or in_array('time',$this->cols)){
            $headings[] = trans('time');
        }
        if(empty($this->cols) or in_array('to_time',$this->cols)){
            $headings[] = trans('to time');
        }
        if(empty($this->cols) or in_array('lat',$this->cols)){
            $headings[] = trans('lat');
        }
        if(empty($this->cols) or in_array('lng',$this->cols)){
            $headings[] = trans('lng');
        }
        if(empty($this->cols) or in_array('location',$this->cols)){
            $headings[] = trans('location');
        }
        if(empty($this->cols) or in_array('has_problem',$this->cols)){
            $headings[] = trans('has problem');
        }
        if(empty($this->cols) or in_array('for_all_items',$this->cols)){
            $headings[] = trans('for_all items');
        }
        if(empty($this->cols) or in_array('items',$this->cols)){
            $headings[] = trans('items');
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
        if(empty($this->cols) or in_array('order_id',$this->cols)){
            $data[] = $model->order_id;
        }
        if(empty($this->cols) or in_array('representative_id',$this->cols)){
            $data[] = $model->representative_id;
        }
        if(empty($this->cols) or in_array('type',$this->cols)){
            $data[] = $model->type;
        }
        if(empty($this->cols) or in_array('date',$this->cols)){
            $data[] = $model->date;
        }
        if(empty($this->cols) or in_array('time',$this->cols)){
            $data[] = $model->time;
        }
        if(empty($this->cols) or in_array('to_time',$this->cols)){
            $data[] = $model->to_time;
        }
        if(empty($this->cols) or in_array('lat',$this->cols)){
            $data[] = $model->lat;
        }
        if(empty($this->cols) or in_array('lng',$this->cols)){
            $data[] = $model->lng;
        }
        if(empty($this->cols) or in_array('location',$this->cols)){
            $data[] = $model->location;
        }
        if(empty($this->cols) or in_array('has_problem',$this->cols)){
            $data[] = $model->has_problem;
        }
        if(empty($this->cols) or in_array('for_all_items',$this->cols)){
            $data[] = $model->for_all_items;
        }
        if(empty($this->cols) or in_array('items',$this->cols)){
            $data[] = $model->items->pluck('id')->toArray();
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

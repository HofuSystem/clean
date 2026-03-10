<?php

namespace Core\Orders\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Orders\Models\OrderSchedule;


class OrderSchedulesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return OrderSchedule::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('client_id',$this->cols)){
            $headings[] = trans('client');
        }
        if(empty($this->cols) or in_array('type',$this->cols)){
            $headings[] = trans('type');
        }
        if(empty($this->cols) or in_array('receiver_day',$this->cols)){
            $headings[] = trans('receiver day');
        }
        if(empty($this->cols) or in_array('receiver_date',$this->cols)){
            $headings[] = trans('receiver date');
        }
        if(empty($this->cols) or in_array('receiver_time',$this->cols)){
            $headings[] = trans('receiver time');
        }
        if(empty($this->cols) or in_array('receiver_to_time',$this->cols)){
            $headings[] = trans('receiver to time');
        }
        if(empty($this->cols) or in_array('delivery_day',$this->cols)){
            $headings[] = trans('delivery day');
        }
        if(empty($this->cols) or in_array('delivery_date',$this->cols)){
            $headings[] = trans('delivery date');
        }
        if(empty($this->cols) or in_array('delivery_time',$this->cols)){
            $headings[] = trans('delivery time');
        }
        if(empty($this->cols) or in_array('delivery_to_time',$this->cols)){
            $headings[] = trans('delivery to time');
        }
        if(empty($this->cols) or in_array('receiver_address_id',$this->cols)){
            $headings[] = trans('receiver address');
        }
        if(empty($this->cols) or in_array('delivery_address_id',$this->cols)){
            $headings[] = trans('delivery address');
        }
        if(empty($this->cols) or in_array('note',$this->cols)){
            $headings[] = trans('note');
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
        if(empty($this->cols) or in_array('client_id',$this->cols)){
            $data[] = $model->client_id;
        }
        if(empty($this->cols) or in_array('type',$this->cols)){
            $data[] = $model->type;
        }
        if(empty($this->cols) or in_array('receiver_day',$this->cols)){
            $data[] = $model->receiver_day;
        }
        if(empty($this->cols) or in_array('receiver_date',$this->cols)){
            $data[] = $model->receiver_date;
        }
        if(empty($this->cols) or in_array('receiver_time',$this->cols)){
            $data[] = $model->receiver_time;
        }
        if(empty($this->cols) or in_array('receiver_to_time',$this->cols)){
            $data[] = $model->receiver_to_time;
        }
        if(empty($this->cols) or in_array('delivery_day',$this->cols)){
            $data[] = $model->delivery_day;
        }
        if(empty($this->cols) or in_array('delivery_date',$this->cols)){
            $data[] = $model->delivery_date;
        }
        if(empty($this->cols) or in_array('delivery_time',$this->cols)){
            $data[] = $model->delivery_time;
        }
        if(empty($this->cols) or in_array('delivery_to_time',$this->cols)){
            $data[] = $model->delivery_to_time;
        }
        if(empty($this->cols) or in_array('receiver_address_id',$this->cols)){
            $data[] = $model->receiver_address_id;
        }
        if(empty($this->cols) or in_array('delivery_address_id',$this->cols)){
            $data[] = $model->delivery_address_id;
        }
        if(empty($this->cols) or in_array('note',$this->cols)){
            $data[] = $model->note;
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

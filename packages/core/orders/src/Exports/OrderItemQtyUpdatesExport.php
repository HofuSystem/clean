<?php

namespace Core\Orders\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Orders\Models\OrderItemQtyUpdate;


class OrderItemQtyUpdatesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return OrderItemQtyUpdate::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('item_id',$this->cols)){
            $headings[] = trans('item');
        }
        if(empty($this->cols) or in_array('from',$this->cols)){
            $headings[] = trans('from');
        }
        if(empty($this->cols) or in_array('to',$this->cols)){
            $headings[] = trans('to');
        }
        if(empty($this->cols) or in_array('updater_email',$this->cols)){
            $headings[] = trans('updater email');
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
        if(empty($this->cols) or in_array('item_id',$this->cols)){
            $data[] = $model->item_id;
        }
        if(empty($this->cols) or in_array('from',$this->cols)){
            $data[] = $model->from;
        }
        if(empty($this->cols) or in_array('to',$this->cols)){
            $data[] = $model->to;
        }
        if(empty($this->cols) or in_array('updater_email',$this->cols)){
            $data[] = $model->updater_email;
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

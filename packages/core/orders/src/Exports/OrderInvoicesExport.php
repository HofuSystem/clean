<?php

namespace Core\Orders\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Orders\Models\OrderInvoice;


class OrderInvoicesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return OrderInvoice::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('invoice_num',$this->cols)){
            $headings[] = trans('invoice num');
        }
        if(empty($this->cols) or in_array('data',$this->cols)){
            $headings[] = trans('data');
        }
        if(empty($this->cols) or in_array('order_id',$this->cols)){
            $headings[] = trans('order');
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
        if(empty($this->cols) or in_array('invoice_num',$this->cols)){
            $data[] = $model->invoice_num;
        }
        if(empty($this->cols) or in_array('data',$this->cols)){
            $data[] = $model->data;
        }
        if(empty($this->cols) or in_array('order_id',$this->cols)){
            $data[] = $model->order_id;
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

<?php

namespace Core\Orders\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Orders\Models\OrderItem;


class OrderItemsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return OrderItem::get();
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
        if(empty($this->cols) or in_array('product_id',$this->cols)){
            $headings[] = trans('product');
        }
        if(empty($this->cols) or in_array('product_data',$this->cols)){
            $headings[] = trans('product data');
        }
        if(empty($this->cols) or in_array('product_price',$this->cols)){
            $headings[] = trans('product price');
        }
        if(empty($this->cols) or in_array('quantity',$this->cols)){
            $headings[] = trans('quantity');
        }
        if(empty($this->cols) or in_array('width',$this->cols)){
            $headings[] = trans('width');
        }
        if(empty($this->cols) or in_array('height',$this->cols)){
            $headings[] = trans('height');
        }
        if(empty($this->cols) or in_array('add_by_admin',$this->cols)){
            $headings[] = trans('add by admin');
        }
        if(empty($this->cols) or in_array('update_by_admin',$this->cols)){
            $headings[] = trans('update by admin');
        }
        if(empty($this->cols) or in_array('is_picked',$this->cols)){
            $headings[] = trans('is picked');
        }
        if(empty($this->cols) or in_array('is_delivered',$this->cols)){
            $headings[] = trans('is delivered');
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
        if(empty($this->cols) or in_array('product_id',$this->cols)){
            $data[] = $model->product_id;
        }
        if(empty($this->cols) or in_array('product_data',$this->cols)){
            $data[] = $model->product_data;
        }
        if(empty($this->cols) or in_array('product_price',$this->cols)){
            $data[] = $model->product_price;
        }
        if(empty($this->cols) or in_array('quantity',$this->cols)){
            $data[] = $model->quantity;
        }
        if(empty($this->cols) or in_array('width',$this->cols)){
            $data[] = $model->width;
        }
        if(empty($this->cols) or in_array('height',$this->cols)){
            $data[] = $model->height;
        }
        if(empty($this->cols) or in_array('add_by_admin',$this->cols)){
            $data[] = $model->add_by_admin;
        }
        if(empty($this->cols) or in_array('update_by_admin',$this->cols)){
            $data[] = $model->update_by_admin;
        }
        if(empty($this->cols) or in_array('is_picked',$this->cols)){
            $data[] = $model->is_picked;
        }
        if(empty($this->cols) or in_array('is_delivered',$this->cols)){
            $data[] = $model->is_delivered;
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

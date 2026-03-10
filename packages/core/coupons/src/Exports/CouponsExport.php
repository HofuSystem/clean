<?php

namespace Core\Coupons\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Coupons\Models\Coupon;


class CouponsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Coupon::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $headings[] = trans('title').'(en) translations.en.title';
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $headings[] = trans('title').'(ar) translations.ar.title';
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $headings[] = trans('status');
        }
        if(empty($this->cols) or in_array('applying',$this->cols)){
            $headings[] = trans('applying');
        }
        if(empty($this->cols) or in_array('code',$this->cols)){
            $headings[] = trans('code');
        }
        if(empty($this->cols) or in_array('max_use',$this->cols)){
            $headings[] = trans('max use');
        }
        if(empty($this->cols) or in_array('max_use_per_user',$this->cols)){
            $headings[] = trans('max use per user');
        }
        if(empty($this->cols) or in_array('payment_method',$this->cols)){
            $headings[] = trans('payment method');
        }
        if(empty($this->cols) or in_array('start_at',$this->cols)){
            $headings[] = trans('start at');
        }
        if(empty($this->cols) or in_array('end_at',$this->cols)){
            $headings[] = trans('end at');
        }
        if(empty($this->cols) or in_array('order_type',$this->cols)){
            $headings[] = trans('order Type');
        }
        if(empty($this->cols) or in_array('all_products',$this->cols)){
            $headings[] = trans('all products');
        }
        if(empty($this->cols) or in_array('products',$this->cols)){
            $headings[] = trans('products');
        }
        if(empty($this->cols) or in_array('categories',$this->cols)){
            $headings[] = trans('categories');
        }
        if(empty($this->cols) or in_array('all_users',$this->cols)){
            $headings[] = trans('all users');
        }
        if(empty($this->cols) or in_array('users',$this->cols)){
            $headings[] = trans('users');
        }
        if(empty($this->cols) or in_array('roles',$this->cols)){
            $headings[] = trans('roles');
        }
        if(empty($this->cols) or in_array('order_minimum',$this->cols)){
            $headings[] = trans('order minimum');
        }
        if(empty($this->cols) or in_array('order_maximum',$this->cols)){
            $headings[] = trans('order maximum');
        }
        if(empty($this->cols) or in_array('type',$this->cols)){
            $headings[] = trans('type');
        }
        if(empty($this->cols) or in_array('value',$this->cols)){
            $headings[] = trans('value');
        }
        if(empty($this->cols) or in_array('max_value',$this->cols)){
            $headings[] = trans('max value');
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
        if(empty($this->cols) or in_array('title',$this->cols)){
            $data[] = $model->translate('en')->title;
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $data[] = $model->translate('ar')->title;
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $data[] = $model->status;
        }
        if(empty($this->cols) or in_array('applying',$this->cols)){
            $data[] = $model->applying;
        }
        if(empty($this->cols) or in_array('code',$this->cols)){
            $data[] = $model->code;
        }
        if(empty($this->cols) or in_array('max_use',$this->cols)){
            $data[] = $model->max_use;
        }
        if(empty($this->cols) or in_array('max_use_per_user',$this->cols)){
            $data[] = $model->max_use_per_user;
        }
        if(empty($this->cols) or in_array('payment_method',$this->cols)){
            $data[] = $model->payment_method;
        }
        if(empty($this->cols) or in_array('start_at',$this->cols)){
            $data[] = $model->start_at;
        }
        if(empty($this->cols) or in_array('end_at',$this->cols)){
            $data[] = $model->end_at;
        }
        if(empty($this->cols) or in_array('order_type',$this->cols)){
            $data[] = $model->order_type;
        }
        if(empty($this->cols) or in_array('all_products',$this->cols)){
            $data[] = $model->all_products;
        }
        if(empty($this->cols) or in_array('products',$this->cols)){
            $data[] = $model->products->pluck('id')->toArray();
        }
        if(empty($this->cols) or in_array('categories',$this->cols)){
            $data[] = $model->categories->pluck('id')->toArray();
        }
        if(empty($this->cols) or in_array('all_users',$this->cols)){
            $data[] = $model->all_users;
        }
        if(empty($this->cols) or in_array('users',$this->cols)){
            $data[] = $model->users->pluck('id')->toArray();
        }
        if(empty($this->cols) or in_array('roles',$this->cols)){
            $data[] = $model->roles->pluck('id')->toArray();
        }
        if(empty($this->cols) or in_array('order_minimum',$this->cols)){
            $data[] = $model->order_minimum;
        }
        if(empty($this->cols) or in_array('order_maximum',$this->cols)){
            $data[] = $model->order_maximum;
        }
        if(empty($this->cols) or in_array('type',$this->cols)){
            $data[] = $model->type;
        }
        if(empty($this->cols) or in_array('value',$this->cols)){
            $data[] = $model->value;
        }
        if(empty($this->cols) or in_array('max_value',$this->cols)){
            $data[] = $model->max_value;
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

<?php

namespace Core\Orders\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Orders\Models\Order;


class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Order::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('reference_id',$this->cols)){
            $headings[] = trans('reference_id');
        }
        if(empty($this->cols) or in_array('type',$this->cols)){
            $headings[] = trans('type');
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $headings[] = trans('status');
        }
        if(empty($this->cols) or in_array('client_id',$this->cols)){
            $headings[] = trans('client');
        }
        if(empty($this->cols) or in_array('pay_type',$this->cols)){
            $headings[] = trans('pay type');
        }
        if(empty($this->cols) or in_array('transaction_id',$this->cols)){
            $headings[] = trans('transaction id');
        }
        if(empty($this->cols) or in_array('order_status_times',$this->cols)){
            $headings[] = trans('order status times');
        }
        if(empty($this->cols) or in_array('days_per_week',$this->cols)){
            $headings[] = trans('days per week');
        }
        if(empty($this->cols) or in_array('days_per_week_names',$this->cols)){
            $headings[] = trans('days per week names');
        }
        if(empty($this->cols) or in_array('days_per_month_dates',$this->cols)){
            $headings[] = trans('days per month names');
        }
        if(empty($this->cols) or in_array('note',$this->cols)){
            $headings[] = trans('note');
        }
        if(empty($this->cols) or in_array('coupon_id',$this->cols)){
            $headings[] = trans('coupon');
        }
        if(empty($this->cols) or in_array('coupon_data',$this->cols)){
            $headings[] = trans('coupon data');
        }
        if(empty($this->cols) or in_array('order_price',$this->cols)){
            $headings[] = trans('order price ');
        }
        if(empty($this->cols) or in_array('delivery_price',$this->cols)){
            $headings[] = trans('delivery price ');
        }
        if(empty($this->cols) or in_array('total_price',$this->cols)){
            $headings[] = trans('total price');
        }
        if(empty($this->cols) or in_array('paid',$this->cols)){
            $headings[] = trans('paid');
        }
        if(empty($this->cols) or in_array('is_admin_accepted',$this->cols)){
            $headings[] = trans('is admin accepted');
        }
        if(empty($this->cols) or in_array('admin_cancel_reason',$this->cols)){
            $headings[] = trans('admin cancel reason');
        }
        if(empty($this->cols) or in_array('wallet_used',$this->cols)){
            $headings[] = trans('wallet used');
        }
        if(empty($this->cols) or in_array('wallet_amount_used',$this->cols)){
            $headings[] = trans('wallet amount used');
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
        if(empty($this->cols) or in_array('reference_id',$this->cols)){
            $data[] = $model->reference_id;
        }
        if(empty($this->cols) or in_array('type',$this->cols)){
            $data[] = $model->type;
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $data[] = $model->status;
        }
        if(empty($this->cols) or in_array('client_id',$this->cols)){
            $data[] = $model->client_id;
        }
        if(empty($this->cols) or in_array('pay_type',$this->cols)){
            $data[] = $model->pay_type;
        }
        if(empty($this->cols) or in_array('transaction_id',$this->cols)){
            $data[] = $model->transaction_id;
        }
        if(empty($this->cols) or in_array('order_status_times',$this->cols)){
            $data[] = $model->order_status_times;
        }
        if(empty($this->cols) or in_array('days_per_week',$this->cols)){
            $data[] = $model->days_per_week;
        }
        if(empty($this->cols) or in_array('days_per_week_names',$this->cols)){
            $data[] = $model->days_per_week_names;
        }
        if(empty($this->cols) or in_array('days_per_month_dates',$this->cols)){
            $data[] = $model->days_per_month_dates;
        }
        if(empty($this->cols) or in_array('note',$this->cols)){
            $data[] = $model->note;
        }
        if(empty($this->cols) or in_array('coupon_id',$this->cols)){
            $data[] = $model->coupon_id;
        }
        if(empty($this->cols) or in_array('coupon_data',$this->cols)){
            $data[] = $model->coupon_data;
        }
        if(empty($this->cols) or in_array('order_price',$this->cols)){
            $data[] = $model->order_price;
        }
        if(empty($this->cols) or in_array('delivery_price',$this->cols)){
            $data[] = $model->delivery_price;
        }
        if(empty($this->cols) or in_array('total_price',$this->cols)){
            $data[] = $model->total_price;
        }
        if(empty($this->cols) or in_array('paid',$this->cols)){
            $data[] = $model->paid;
        }
        if(empty($this->cols) or in_array('is_admin_accepted',$this->cols)){
            $data[] = $model->is_admin_accepted;
        }
        if(empty($this->cols) or in_array('admin_cancel_reason',$this->cols)){
            $data[] = $model->admin_cancel_reason;
        }
        if(empty($this->cols) or in_array('wallet_used',$this->cols)){
            $data[] = $model->wallet_used;
        }
        if(empty($this->cols) or in_array('wallet_amount_used',$this->cols)){
            $data[] = $model->wallet_amount_used;
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

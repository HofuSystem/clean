<?php

namespace Core\PaymentGateways\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\PaymentGateways\Models\PaymentTransaction;


class PaymentTransactionsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return PaymentTransaction::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('transaction_id',$this->cols)){
            $headings[] = trans('transaction id');
        }
        if(empty($this->cols) or in_array('for',$this->cols)){
            $headings[] = trans('for');
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $headings[] = trans('status');
        }
        if(empty($this->cols) or in_array('request_data',$this->cols)){
            $headings[] = trans('request data');
        }
        if(empty($this->cols) or in_array('payment_method',$this->cols)){
            $headings[] = trans('payment method');
        }
        if(empty($this->cols) or in_array('payment_data',$this->cols)){
            $headings[] = trans('payment data');
        }
        if(empty($this->cols) or in_array('payment_response',$this->cols)){
            $headings[] = trans('payment response');
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
        if(empty($this->cols) or in_array('transaction_id',$this->cols)){
            $data[] = $model->transaction_id;
        }
        if(empty($this->cols) or in_array('for',$this->cols)){
            $data[] = $model->for;
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $data[] = $model->status;
        }
        if(empty($this->cols) or in_array('request_data',$this->cols)){
            $data[] = $model->request_data;
        }
        if(empty($this->cols) or in_array('payment_method',$this->cols)){
            $data[] = $model->payment_method;
        }
        if(empty($this->cols) or in_array('payment_data',$this->cols)){
            $data[] = $model->payment_data;
        }
        if(empty($this->cols) or in_array('payment_response',$this->cols)){
            $data[] = $model->payment_response;
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

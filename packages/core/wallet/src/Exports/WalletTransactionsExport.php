<?php

namespace Core\Wallet\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Wallet\Models\WalletTransaction;


class WalletTransactionsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return WalletTransaction::get();
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
        if(empty($this->cols) or in_array('amount',$this->cols)){
            $headings[] = trans('amount');
        }
        if(empty($this->cols) or in_array('wallet_before',$this->cols)){
            $headings[] = trans('wallet before');
        }
        if(empty($this->cols) or in_array('wallet_after',$this->cols)){
            $headings[] = trans('wallet after');
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $headings[] = trans('status');
        }
        if(empty($this->cols) or in_array('transaction_id',$this->cols)){
            $headings[] = trans('transaction id');
        }
        if(empty($this->cols) or in_array('bank_name',$this->cols)){
            $headings[] = trans('bank name');
        }
        if(empty($this->cols) or in_array('account_number',$this->cols)){
            $headings[] = trans('account number');
        }
        if(empty($this->cols) or in_array('iban_number',$this->cols)){
            $headings[] = trans('iban number');
        }
        if(empty($this->cols) or in_array('user_id',$this->cols)){
            $headings[] = trans('user');
        }
        if(empty($this->cols) or in_array('added_by_id',$this->cols)){
            $headings[] = trans('added by');
        }
        if(empty($this->cols) or in_array('package_id',$this->cols)){
            $headings[] = trans('package');
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
        if(empty($this->cols) or in_array('amount',$this->cols)){
            $data[] = $model->amount;
        }
        if(empty($this->cols) or in_array('wallet_before',$this->cols)){
            $data[] = $model->wallet_before;
        }
        if(empty($this->cols) or in_array('wallet_after',$this->cols)){
            $data[] = $model->wallet_after;
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $data[] = $model->status;
        }
        if(empty($this->cols) or in_array('transaction_id',$this->cols)){
            $data[] = $model->transaction_id;
        }
        if(empty($this->cols) or in_array('bank_name',$this->cols)){
            $data[] = $model->bank_name;
        }
        if(empty($this->cols) or in_array('account_number',$this->cols)){
            $data[] = $model->account_number;
        }
        if(empty($this->cols) or in_array('iban_number',$this->cols)){
            $data[] = $model->iban_number;
        }
        if(empty($this->cols) or in_array('user_id',$this->cols)){
            $data[] = $model->user_id;
        }
        if(empty($this->cols) or in_array('added_by_id',$this->cols)){
            $data[] = $model->added_by_id;
        }
        if(empty($this->cols) or in_array('package_id',$this->cols)){
            $data[] = $model->package_id;
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

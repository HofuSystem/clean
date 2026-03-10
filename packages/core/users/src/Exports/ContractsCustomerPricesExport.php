<?php

namespace Core\Users\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Users\Models\ContractsCustomerPrice;


class ContractsCustomerPricesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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

        // Fetch data from the database
        return ContractsCustomerPrice::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('contract_id',$this->cols)){
            $headings[] = trans('contract');
        }
        if(empty($this->cols) or in_array('product_id',$this->cols)){
            $headings[] = trans('product');
        }
        if(empty($this->cols) or in_array('over_price',$this->cols)){
            $headings[] = trans('over price');
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
        if(empty($this->cols) or in_array('contract_id',$this->cols)){
            $data[] = $model->contract_id;
        }
        if(empty($this->cols) or in_array('product_id',$this->cols)){
            $data[] = $model->product_id;
        }
        if(empty($this->cols) or in_array('over_price',$this->cols)){
            $data[] = $model->over_price;
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


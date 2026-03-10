<?php

namespace Core\Users\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Users\Models\Contract;


class ContractsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Contract::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $headings[] = trans('title');
        }
        if(empty($this->cols) or in_array('months_count',$this->cols)){
            $headings[] = trans('months count');
        }
        if(empty($this->cols) or in_array('month_fees',$this->cols)){
            $headings[] = trans('month fees');
        }
        if(empty($this->cols) or in_array('unlimited_days',$this->cols)){
            $headings[] = trans('unlimited days');
        }
        if(empty($this->cols) or in_array('number_of_days',$this->cols)){
            $headings[] = trans('number of days');
        }
        if(empty($this->cols) or in_array('contract',$this->cols)){
            $headings[] = trans('contract');
        }
        if(empty($this->cols) or in_array('start_date',$this->cols)){
            $headings[] = trans('start date');
        }
        if(empty($this->cols) or in_array('end_date',$this->cols)){
            $headings[] = trans('end date');
        }
        if(empty($this->cols) or in_array('client_id',$this->cols)){
            $headings[] = trans('client');
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
            $data[] = $model->title;
        }
        if(empty($this->cols) or in_array('months_count',$this->cols)){
            $data[] = $model->months_count;
        }
        if(empty($this->cols) or in_array('month_fees',$this->cols)){
            $data[] = $model->month_fees;
        }
        if(empty($this->cols) or in_array('unlimited_days',$this->cols)){
            $data[] = $model->unlimited_days;
        }
        if(empty($this->cols) or in_array('number_of_days',$this->cols)){
            $data[] = $model->number_of_days;
        }
        if(empty($this->cols) or in_array('contract',$this->cols)){
            $data[] = $model->contract;
        }
        if(empty($this->cols) or in_array('start_date',$this->cols)){
            $data[] = $model->start_date;
        }
        if(empty($this->cols) or in_array('end_date',$this->cols)){
            $data[] = $model->end_date;
        }
        if(empty($this->cols) or in_array('client_id',$this->cols)){
            $data[] = $model->client_id;
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

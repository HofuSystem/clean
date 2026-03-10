<?php

namespace Core\Admin\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Admin\Models\RoutesRecord;


class RoutesRecordsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return RoutesRecord::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('end_point',$this->cols)){
            $headings[] = trans('end point');
        }
        if(empty($this->cols) or in_array('attributes',$this->cols)){
            $headings[] = trans('attributes');
        }
        if(empty($this->cols) or in_array('user_id',$this->cols)){
            $headings[] = trans('user');
        }
        if(empty($this->cols) or in_array('ip_address',$this->cols)){
            $headings[] = trans('ip_address');
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
        if(empty($this->cols) or in_array('end_point',$this->cols)){
            $data[] = $model->end_point;
        }
        if(empty($this->cols) or in_array('attributes',$this->cols)){
            $data[] = $model->attributes;
        }
        if(empty($this->cols) or in_array('user_id',$this->cols)){
            $data[] = $model->user_id;
        }
        if(empty($this->cols) or in_array('ip_address',$this->cols)){
            $data[] = $model->ip_address;
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

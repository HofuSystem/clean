<?php

namespace Core\Users\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Users\Models\Ban;


class BansExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Ban::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('level',$this->cols)){
            $headings[] = trans('level');
        }
        if(empty($this->cols) or in_array('value',$this->cols)){
            $headings[] = trans('value');
        }
        if(empty($this->cols) or in_array('admin_id',$this->cols)){
            $headings[] = trans('admin');
        }
        if(empty($this->cols) or in_array('reason',$this->cols)){
            $headings[] = trans('reason');
        }
        if(empty($this->cols) or in_array('from',$this->cols)){
            $headings[] = trans('starts');
        }
        if(empty($this->cols) or in_array('to',$this->cols)){
            $headings[] = trans('ends');
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
        if(empty($this->cols) or in_array('level',$this->cols)){
            $data[] = $model->level;
        }
        if(empty($this->cols) or in_array('value',$this->cols)){
            $data[] = $model->value;
        }
        if(empty($this->cols) or in_array('admin_id',$this->cols)){
            $data[] = $model->admin_id;
        }
        if(empty($this->cols) or in_array('reason',$this->cols)){
            $data[] = $model->reason;
        }
        if(empty($this->cols) or in_array('from',$this->cols)){
            $data[] = $model->from;
        }
        if(empty($this->cols) or in_array('to',$this->cols)){
            $data[] = $model->to;
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

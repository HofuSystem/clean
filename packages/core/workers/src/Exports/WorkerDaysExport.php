<?php

namespace Core\Workers\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Workers\Models\WorkerDay;


class WorkerDaysExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return WorkerDay::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('worker_id',$this->cols)){
            $headings[] = trans('worker');
        }
        if(empty($this->cols) or in_array('date',$this->cols)){
            $headings[] = trans('date');
        }
        if(empty($this->cols) or in_array('type',$this->cols)){
            $headings[] = trans('type');
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
        if(empty($this->cols) or in_array('worker_id',$this->cols)){
            $data[] = $model->worker_id;
        }
        if(empty($this->cols) or in_array('date',$this->cols)){
            $data[] = $model->date;
        }
        if(empty($this->cols) or in_array('type',$this->cols)){
            $data[] = $model->type;
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

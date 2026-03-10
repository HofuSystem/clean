<?php

namespace Core\Users\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Users\Models\UserEditRequest;


class UserEditRequestsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return UserEditRequest::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('fullname',$this->cols)){
            $headings[] = trans('full name');
        }
        if(empty($this->cols) or in_array('email',$this->cols)){
            $headings[] = trans('email');
        }
        if(empty($this->cols) or in_array('phone',$this->cols)){
            $headings[] = trans('phone');
        }
        if(empty($this->cols) or in_array('user_id',$this->cols)){
            $headings[] = trans('user');
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
        if(empty($this->cols) or in_array('fullname',$this->cols)){
            $data[] = $model->fullname;
        }
        if(empty($this->cols) or in_array('email',$this->cols)){
            $data[] = $model->email;
        }
        if(empty($this->cols) or in_array('phone',$this->cols)){
            $data[] = $model->phone;
        }
        if(empty($this->cols) or in_array('user_id',$this->cols)){
            $data[] = $model->user_id;
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

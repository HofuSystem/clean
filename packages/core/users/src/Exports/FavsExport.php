<?php

namespace Core\Users\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Users\Models\Fav;


class FavsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Fav::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('favs_type',$this->cols)){
            $headings[] = trans('favs type');
        }
        if(empty($this->cols) or in_array('favs_id',$this->cols)){
            $headings[] = trans('favs');
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
        if(empty($this->cols) or in_array('favs_type',$this->cols)){
            $data[] = $model->favs_type;
        }
        if(empty($this->cols) or in_array('favs_id',$this->cols)){
            $data[] = $model->favs_id;
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

<?php

namespace Core\Categories\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Categories\Models\Price;


class PricesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Price::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('priceable_type',$this->cols)){
            $headings[] = trans('priceable');
        }
        if(empty($this->cols) or in_array('priceable_id',$this->cols)){
            $headings[] = trans('priceable id');
        }
        if(empty($this->cols) or in_array('city_id',$this->cols)){
            $headings[] = trans('city');
        }
        if(empty($this->cols) or in_array('price',$this->cols)){
            $headings[] = trans('price');
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
        if(empty($this->cols) or in_array('priceable_type',$this->cols)){
            $data[] = $model->priceable_type;
        }
        if(empty($this->cols) or in_array('priceable_id',$this->cols)){
            $data[] = $model->priceable_id;
        }
        if(empty($this->cols) or in_array('city_id',$this->cols)){
            $data[] = $model->city_id;
        }
        if(empty($this->cols) or in_array('price',$this->cols)){
            $data[] = $model->price;
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

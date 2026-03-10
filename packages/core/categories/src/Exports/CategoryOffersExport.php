<?php

namespace Core\Categories\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Categories\Models\CategoryOffer;


class CategoryOffersExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return CategoryOffer::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $headings[] = trans('name').'(en) translations.en.name';
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $headings[] = trans('name').'(ar) translations.ar.name';
        }
        if(empty($this->cols) or in_array('desc',$this->cols)){
            $headings[] = trans('desc').'(en) translations.en.desc';
        }
        if(empty($this->cols) or in_array('desc',$this->cols)){
            $headings[] = trans('desc').'(ar) translations.ar.desc';
        }
        if(empty($this->cols) or in_array('price',$this->cols)){
            $headings[] = trans('price');
        }
        if(empty($this->cols) or in_array('sale_price',$this->cols)){
            $headings[] = trans('sale price');
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $headings[] = trans('image');
        }
        if(empty($this->cols) or in_array('hours_num',$this->cols)){
            $headings[] = trans('hours num');
        }
        if(empty($this->cols) or in_array('workers_num',$this->cols)){
            $headings[] = trans('workers num');
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $headings[] = trans('status');
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
        if(empty($this->cols) or in_array('name',$this->cols)){
            $data[] = $model->translate('en')->name;
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $data[] = $model->translate('ar')->name;
        }
        if(empty($this->cols) or in_array('desc',$this->cols)){
            $data[] = $model->translate('en')->desc;
        }
        if(empty($this->cols) or in_array('desc',$this->cols)){
            $data[] = $model->translate('ar')->desc;
        }
        if(empty($this->cols) or in_array('price',$this->cols)){
            $data[] = $model->price;
        }
        if(empty($this->cols) or in_array('sale_price',$this->cols)){
            $data[] = $model->sale_price;
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $data[] = $model->image;
        }
        if(empty($this->cols) or in_array('hours_num',$this->cols)){
            $data[] = $model->hours_num;
        }
        if(empty($this->cols) or in_array('workers_num',$this->cols)){
            $data[] = $model->workers_num;
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $data[] = $model->status;
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

<?php

namespace Core\Categories\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Categories\Models\Category;


class CategoriesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Category::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('slug',$this->cols)){
            $headings[] = trans('slug');
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $headings[] = trans('name').'(en) translations.en.name';
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $headings[] = trans('name').'(ar) translations.ar.name';
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $headings[] = trans('image');
        }
        if(empty($this->cols) or in_array('intro',$this->cols)){
            $headings[] = trans('intro').'(en) translations.en.intro';
        }
        if(empty($this->cols) or in_array('intro',$this->cols)){
            $headings[] = trans('intro').'(ar) translations.ar.intro';
        }
        if(empty($this->cols) or in_array('desc',$this->cols)){
            $headings[] = trans('desc').'(en) translations.en.desc';
        }
        if(empty($this->cols) or in_array('desc',$this->cols)){
            $headings[] = trans('desc').'(ar) translations.ar.desc';
        }
        if(empty($this->cols) or in_array('type',$this->cols)){
            $headings[] = trans('type');
        }
        if(empty($this->cols) or in_array('delivery_price',$this->cols)){
            $headings[] = trans('delivery price');
        }
        if(empty($this->cols) or in_array('sort',$this->cols)){
            $headings[] = trans('sort');
        }
        if(empty($this->cols) or in_array('is_package',$this->cols)){
            $headings[] = trans('is package');
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $headings[] = trans('status');
        }
        if(empty($this->cols) or in_array('parent_id',$this->cols)){
            $headings[] = trans('Parent Category');
        }
        if(empty($this->cols) or in_array('city_id',$this->cols)){
            $headings[] = trans('city');
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
        if(empty($this->cols) or in_array('slug',$this->cols)){
            $data[] = $model->slug;
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $data[] = $model->translate('en')->name;
        }
        if(empty($this->cols) or in_array('name',$this->cols)){
            $data[] = $model->translate('ar')->name;
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $data[] = $model->image;
        }
        if(empty($this->cols) or in_array('intro',$this->cols)){
            $data[] = $model->translate('en')->intro;
        }
        if(empty($this->cols) or in_array('intro',$this->cols)){
            $data[] = $model->translate('ar')->intro;
        }
        if(empty($this->cols) or in_array('desc',$this->cols)){
            $data[] = $model->translate('en')->desc;
        }
        if(empty($this->cols) or in_array('desc',$this->cols)){
            $data[] = $model->translate('ar')->desc;
        }
        if(empty($this->cols) or in_array('type',$this->cols)){
            $data[] = $model->type;
        }
        if(empty($this->cols) or in_array('delivery_price',$this->cols)){
            $data[] = $model->delivery_price;
        }
        if(empty($this->cols) or in_array('sort',$this->cols)){
            $data[] = $model->sort;
        }
        if(empty($this->cols) or in_array('is_package',$this->cols)){
            $data[] = $model->is_package;
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $data[] = $model->status;
        }
        if(empty($this->cols) or in_array('parent_id',$this->cols)){
            $data[] = $model->parent_id;
        }
        if(empty($this->cols) or in_array('city_id',$this->cols)){
            $data[] = $model->city_id;
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

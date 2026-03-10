<?php

namespace Core\Blog\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Blog\Models\Blog;


class BlogsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Blog::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $headings[] = trans('title').'(en) translations.en.title';
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $headings[] = trans('title').'(ar) translations.ar.title';
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $headings[] = trans('image');
        }
        if(empty($this->cols) or in_array('gallery',$this->cols)){
            $headings[] = trans('gallary');
        }
        if(empty($this->cols) or in_array('content',$this->cols)){
            $headings[] = trans('content').'(en) translations.en.content';
        }
        if(empty($this->cols) or in_array('content',$this->cols)){
            $headings[] = trans('content').'(ar) translations.ar.content';
        }
        if(empty($this->cols) or in_array('category_id',$this->cols)){
            $headings[] = trans('category');
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $headings[] = trans('status');
        }
        if(empty($this->cols) or in_array('published_at',$this->cols)){
            $headings[] = trans('published_at');
        }
        if(empty($this->cols) or in_array('meta',$this->cols)){
            $headings[] = trans('meta').'(en) translations.en.meta';
        }
        if(empty($this->cols) or in_array('meta',$this->cols)){
            $headings[] = trans('meta').'(ar) translations.ar.meta';
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
            $data[] = $model->translate('en')->title;
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $data[] = $model->translate('ar')->title;
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $data[] = $model->image;
        }
        if(empty($this->cols) or in_array('gallery',$this->cols)){
            $data[] = $model->gallery;
        }
        if(empty($this->cols) or in_array('content',$this->cols)){
            $data[] = $model->translate('en')->content;
        }
        if(empty($this->cols) or in_array('content',$this->cols)){
            $data[] = $model->translate('ar')->content;
        }
        if(empty($this->cols) or in_array('category_id',$this->cols)){
            $data[] = $model->category_id;
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $data[] = $model->status;
        }
        if(empty($this->cols) or in_array('published_at',$this->cols)){
            $data[] = $model->published_at;
        }
        if(empty($this->cols) or in_array('meta',$this->cols)){
            $data[] = $model->translate('en')->meta;
        }
        if(empty($this->cols) or in_array('meta',$this->cols)){
            $data[] = $model->translate('ar')->meta;
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

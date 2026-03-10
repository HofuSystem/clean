<?php

namespace Core\Pages\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Pages\Models\Section;


class SectionsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Section::get();
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
        if(empty($this->cols) or in_array('small_title',$this->cols)){
            $headings[] = trans('small title').'(en) translations.en.small_title';
        }
        if(empty($this->cols) or in_array('small_title',$this->cols)){
            $headings[] = trans('small title').'(ar) translations.ar.small_title';
        }
        if(empty($this->cols) or in_array('description',$this->cols)){
            $headings[] = trans('description').'(en) translations.en.description';
        }
        if(empty($this->cols) or in_array('description',$this->cols)){
            $headings[] = trans('description').'(ar) translations.ar.description';
        }
        if(empty($this->cols) or in_array('images',$this->cols)){
            $headings[] = trans('images');
        }
        if(empty($this->cols) or in_array('video',$this->cols)){
            $headings[] = trans('video');
        }
        if(empty($this->cols) or in_array('template',$this->cols)){
            $headings[] = trans('template');
        }
        if(empty($this->cols) or in_array('page_id',$this->cols)){
            $headings[] = trans('page');
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
        if(empty($this->cols) or in_array('small_title',$this->cols)){
            $data[] = $model->translate('en')->small_title;
        }
        if(empty($this->cols) or in_array('small_title',$this->cols)){
            $data[] = $model->translate('ar')->small_title;
        }
        if(empty($this->cols) or in_array('description',$this->cols)){
            $data[] = $model->translate('en')->description;
        }
        if(empty($this->cols) or in_array('description',$this->cols)){
            $data[] = $model->translate('ar')->description;
        }
        if(empty($this->cols) or in_array('images',$this->cols)){
            $data[] = $model->images;
        }
        if(empty($this->cols) or in_array('video',$this->cols)){
            $data[] = $model->video;
        }
        if(empty($this->cols) or in_array('template',$this->cols)){
            $data[] = $model->template;
        }
        if(empty($this->cols) or in_array('page_id',$this->cols)){
            $data[] = $model->page_id;
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

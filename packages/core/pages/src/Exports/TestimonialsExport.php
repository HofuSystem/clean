<?php

namespace Core\Pages\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Pages\Models\Testimonial;


class TestimonialsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Testimonial::get();
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
        if(empty($this->cols) or in_array('avatar',$this->cols)){
            $headings[] = trans('avatar');
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $headings[] = trans('title').'(en) translations.en.title';
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $headings[] = trans('title').'(ar) translations.ar.title';
        }
        if(empty($this->cols) or in_array('body',$this->cols)){
            $headings[] = trans('body').'(en) translations.en.body';
        }
        if(empty($this->cols) or in_array('body',$this->cols)){
            $headings[] = trans('body').'(ar) translations.ar.body';
        }
        if(empty($this->cols) or in_array('location',$this->cols)){
            $headings[] = trans('location').'(en) translations.en.location';
        }
        if(empty($this->cols) or in_array('location',$this->cols)){
            $headings[] = trans('location').'(ar) translations.ar.location';
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
        if(empty($this->cols) or in_array('avatar',$this->cols)){
            $data[] = $model->avatar;
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $data[] = $model->translate('en')->title;
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $data[] = $model->translate('ar')->title;
        }
        if(empty($this->cols) or in_array('body',$this->cols)){
            $data[] = $model->translate('en')->body;
        }
        if(empty($this->cols) or in_array('body',$this->cols)){
            $data[] = $model->translate('ar')->body;
        }
        if(empty($this->cols) or in_array('location',$this->cols)){
            $data[] = $model->translate('en')->location;
        }
        if(empty($this->cols) or in_array('location',$this->cols)){
            $data[] = $model->translate('ar')->location;
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

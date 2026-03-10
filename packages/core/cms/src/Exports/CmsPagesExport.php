<?php

namespace Core\CMS\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\CMS\Models\CmsPage;


class CmsPagesExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return CmsPage::get();
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
        if(empty($this->cols) or in_array('is_parent',$this->cols)){
            $headings[] = trans('is parent');
        }
        if(empty($this->cols) or in_array('is_multi_upload',$this->cols)){
            $headings[] = trans('is multi upload');
        }
        if(empty($this->cols) or in_array('have_point',$this->cols)){
            $headings[] = trans('have point');
        }
        if(empty($this->cols) or in_array('have_name',$this->cols)){
            $headings[] = trans('have name');
        }
        if(empty($this->cols) or in_array('have_description',$this->cols)){
            $headings[] = trans('have description');
        }
        if(empty($this->cols) or in_array('have_intro',$this->cols)){
            $headings[] = trans('have intro');
        }
        if(empty($this->cols) or in_array('have_image',$this->cols)){
            $headings[] = trans('have image');
        }
        if(empty($this->cols) or in_array('have_tablet_image',$this->cols)){
            $headings[] = trans('have tablet image');
        }
        if(empty($this->cols) or in_array('have_mobile_image',$this->cols)){
            $headings[] = trans('have mobile image');
        }
        if(empty($this->cols) or in_array('have_icon',$this->cols)){
            $headings[] = trans('have icon');
        }
        if(empty($this->cols) or in_array('have_video',$this->cols)){
            $headings[] = trans('have video');
        }
        if(empty($this->cols) or in_array('have_link',$this->cols)){
            $headings[] = trans('have link');
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
        if(empty($this->cols) or in_array('is_parent',$this->cols)){
            $data[] = $model->is_parent;
        }
        if(empty($this->cols) or in_array('is_multi_upload',$this->cols)){
            $data[] = $model->is_multi_upload;
        }
        if(empty($this->cols) or in_array('have_point',$this->cols)){
            $data[] = $model->have_point;
        }
        if(empty($this->cols) or in_array('have_name',$this->cols)){
            $data[] = $model->have_name;
        }
        if(empty($this->cols) or in_array('have_description',$this->cols)){
            $data[] = $model->have_description;
        }
        if(empty($this->cols) or in_array('have_intro',$this->cols)){
            $data[] = $model->have_intro;
        }
        if(empty($this->cols) or in_array('have_image',$this->cols)){
            $data[] = $model->have_image;
        }
        if(empty($this->cols) or in_array('have_tablet_image',$this->cols)){
            $data[] = $model->have_tablet_image;
        }
        if(empty($this->cols) or in_array('have_mobile_image',$this->cols)){
            $data[] = $model->have_mobile_image;
        }
        if(empty($this->cols) or in_array('have_icon',$this->cols)){
            $data[] = $model->have_icon;
        }
        if(empty($this->cols) or in_array('have_video',$this->cols)){
            $data[] = $model->have_video;
        }
        if(empty($this->cols) or in_array('have_link',$this->cols)){
            $data[] = $model->have_link;
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

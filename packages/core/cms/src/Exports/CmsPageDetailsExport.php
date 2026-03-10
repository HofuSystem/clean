<?php

namespace Core\CMS\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\CMS\Models\CmsPageDetail;


class CmsPageDetailsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return CmsPageDetail::get();
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
        if(empty($this->cols) or in_array('description',$this->cols)){
            $headings[] = trans('description').'(en) translations.en.description';
        }
        if(empty($this->cols) or in_array('description',$this->cols)){
            $headings[] = trans('description').'(ar) translations.ar.description';
        }
        if(empty($this->cols) or in_array('intro',$this->cols)){
            $headings[] = trans('intro').'(en) translations.en.intro';
        }
        if(empty($this->cols) or in_array('intro',$this->cols)){
            $headings[] = trans('intro').'(ar) translations.ar.intro';
        }
        if(empty($this->cols) or in_array('point',$this->cols)){
            $headings[] = trans('point').'(en) translations.en.point';
        }
        if(empty($this->cols) or in_array('point',$this->cols)){
            $headings[] = trans('point').'(ar) translations.ar.point';
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $headings[] = trans('image');
        }
        if(empty($this->cols) or in_array('tablet_image',$this->cols)){
            $headings[] = trans('tablet image');
        }
        if(empty($this->cols) or in_array('mobile_image',$this->cols)){
            $headings[] = trans('mobile image');
        }
        if(empty($this->cols) or in_array('icon',$this->cols)){
            $headings[] = trans('icon');
        }
        if(empty($this->cols) or in_array('video',$this->cols)){
            $headings[] = trans('video');
        }
        if(empty($this->cols) or in_array('link',$this->cols)){
            $headings[] = trans('link');
        }
        if(empty($this->cols) or in_array('cms_pages_id',$this->cols)){
            $headings[] = trans('cms pages');
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
        if(empty($this->cols) or in_array('description',$this->cols)){
            $data[] = $model->translate('en')->description;
        }
        if(empty($this->cols) or in_array('description',$this->cols)){
            $data[] = $model->translate('ar')->description;
        }
        if(empty($this->cols) or in_array('intro',$this->cols)){
            $data[] = $model->translate('en')->intro;
        }
        if(empty($this->cols) or in_array('intro',$this->cols)){
            $data[] = $model->translate('ar')->intro;
        }
        if(empty($this->cols) or in_array('point',$this->cols)){
            $data[] = $model->translate('en')->point;
        }
        if(empty($this->cols) or in_array('point',$this->cols)){
            $data[] = $model->translate('ar')->point;
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $data[] = $model->image;
        }
        if(empty($this->cols) or in_array('tablet_image',$this->cols)){
            $data[] = $model->tablet_image;
        }
        if(empty($this->cols) or in_array('mobile_image',$this->cols)){
            $data[] = $model->mobile_image;
        }
        if(empty($this->cols) or in_array('icon',$this->cols)){
            $data[] = $model->icon;
        }
        if(empty($this->cols) or in_array('video',$this->cols)){
            $data[] = $model->video;
        }
        if(empty($this->cols) or in_array('link',$this->cols)){
            $data[] = $model->link;
        }
        if(empty($this->cols) or in_array('cms_pages_id',$this->cols)){
            $data[] = $model->cms_pages_id;
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

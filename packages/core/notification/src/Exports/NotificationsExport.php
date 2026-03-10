<?php

namespace Core\Notification\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Notification\Models\Notification;


class NotificationsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Notification::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('types',$this->cols)){
            $headings[] = trans('types');
        }
        if(empty($this->cols) or in_array('for',$this->cols)){
            $headings[] = trans('for');
        }
        if(empty($this->cols) or in_array('for_data',$this->cols)){
            $headings[] = trans('for data');
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $headings[] = trans('title');
        }
        if(empty($this->cols) or in_array('body',$this->cols)){
            $headings[] = trans('body');
        }
        if(empty($this->cols) or in_array('media',$this->cols)){
            $headings[] = trans('media');
        }
        if(empty($this->cols) or in_array('sender_id',$this->cols)){
            $headings[] = trans('sender');
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
        if(empty($this->cols) or in_array('types',$this->cols)){
            $data[] = $model->types;
        }
        if(empty($this->cols) or in_array('for',$this->cols)){
            $data[] = $model->for;
        }
        if(empty($this->cols) or in_array('for_data',$this->cols)){
            $data[] = $model->for_data;
        }
        if(empty($this->cols) or in_array('title',$this->cols)){
            $data[] = $model->title;
        }
        if(empty($this->cols) or in_array('body',$this->cols)){
            $data[] = $model->body;
        }
        if(empty($this->cols) or in_array('media',$this->cols)){
            $data[] = $model->media;
        }
        if(empty($this->cols) or in_array('sender_id',$this->cols)){
            $data[] = $model->sender_id;
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

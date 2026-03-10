<?php

namespace Core\Notification\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Notification\Models\BannerNotification;


class BannerNotificationsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return BannerNotification::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('image',$this->cols)){
            $headings[] = trans('image');
        }
        if(empty($this->cols) or in_array('publish_date',$this->cols)){
            $headings[] = trans('publish date');
        }
        if(empty($this->cols) or in_array('expired_date',$this->cols)){
            $headings[] = trans('expired date');
        }
        if(empty($this->cols) or in_array('next_vision_hour',$this->cols)){
            $headings[] = trans('next vision hour');
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $headings[] = trans('status');
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
        if(empty($this->cols) or in_array('image',$this->cols)){
            $data[] = $model->image;
        }
        if(empty($this->cols) or in_array('publish_date',$this->cols)){
            $data[] = $model->publish_date;
        }
        if(empty($this->cols) or in_array('expired_date',$this->cols)){
            $data[] = $model->expired_date;
        }
        if(empty($this->cols) or in_array('next_vision_hour',$this->cols)){
            $data[] = $model->next_vision_hour;
        }
        if(empty($this->cols) or in_array('status',$this->cols)){
            $data[] = $model->status;
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

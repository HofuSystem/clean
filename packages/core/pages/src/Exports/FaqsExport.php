<?php

namespace Core\Pages\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Core\Pages\Models\Faq;


class FaqsExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
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
        return Faq::get();
    }

    public function headings(): array
    {
        // Define headers based on the provided attributes
        $headings = [];

        if(empty($this->cols) or in_array('id',$this->cols)){
            $headings[] = trans('id');
        }
        if(empty($this->cols) or in_array('question',$this->cols)){
            $headings[] = trans('question').'(en) translations.en.question';
        }
        if(empty($this->cols) or in_array('question',$this->cols)){
            $headings[] = trans('question').'(ar) translations.ar.question';
        }
        if(empty($this->cols) or in_array('answer',$this->cols)){
            $headings[] = trans('answer').'(en) translations.en.answer';
        }
        if(empty($this->cols) or in_array('answer',$this->cols)){
            $headings[] = trans('answer').'(ar) translations.ar.answer';
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
        if(empty($this->cols) or in_array('question',$this->cols)){
            $data[] = $model->translate('en')->question;
        }
        if(empty($this->cols) or in_array('question',$this->cols)){
            $data[] = $model->translate('ar')->question;
        }
        if(empty($this->cols) or in_array('answer',$this->cols)){
            $data[] = $model->translate('en')->answer;
        }
        if(empty($this->cols) or in_array('answer',$this->cols)){
            $data[] = $model->translate('ar')->answer;
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

<?php

namespace Core\Admin\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderQuantitiesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            trans('Product ID'),
            trans('Product Name'),
            trans('Category'),
            trans('Subcategory'),
            trans('wash_type'),
            trans('Total Quantity'),
        ];
    }

    public function map($row): array
    {
        return [
            $row->product_id,
            $row->product_name,
            $row->category_name,
            $row->subcategory_name ?? '-',
            trans($row->wash_type),
            $row->total_quantity,
        ];
    }
}

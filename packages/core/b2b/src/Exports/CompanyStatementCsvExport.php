<?php

namespace Core\B2B\Exports;

use Core\B2B\Models\B2BFinancial;
use Core\Orders\Models\Invoice;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompanyStatementCsvExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $companyId;
    protected $request;

    public function __construct($companyId, Request $request)
    {
        $this->companyId = $companyId;
        $this->request = $request;
    }

    public function array(): array
    {
        $companyId = $this->companyId;
        $request = $this->request;

        $query = B2BFinancial::where('company_id', $companyId)->orderBy('collection_date', 'asc')->orderBy('created_at', 'asc');
        if ($request->from_date) $query->whereDate('collection_date', '>=', $request->from_date);
        if ($request->to_date) $query->whereDate('collection_date', '<=', $request->to_date);

        $financials = $query->get()->map(function ($fin) {
            return [
                'date'         => $fin->collection_date ?? $fin->created_at,
                'reference_id' => $fin->reference_id,
                'note'         => $fin->note,
                'type'         => $fin->type,
                'amount'       => $fin->amount,
                'source'       => 'financial',
            ];
        });

        // Fetch Invoices
        $invoicesQuery = Invoice::whereHas('order', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->orderBy('created_at', 'asc');

        if ($request->from_date) $invoicesQuery->whereDate('created_at', '>=', $request->from_date);
        if ($request->to_date) $invoicesQuery->whereDate('created_at', '<=', $request->to_date);

        $invoices = $invoicesQuery->get()->map(function ($inv) {
            return [
                'date'         => $inv->created_at,
                'reference_id' => $inv->invoice_number,
                'note'         => $inv->invoice_number,
                'type'         => 'invoice',
                'amount'       => $inv->total,
                'source'       => 'invoice',
            ];
        });

        $merged = $financials->concat($invoices)->sortBy(function ($item) {
            return $item['date'];
        });

        $balance = 0;
        $data = [];
        foreach ($merged as $item) {
            $dateStr = $item['date'] instanceof \Carbon\Carbon ? $item['date']->format('Y-m-d') : substr($item['date'], 0, 10);
            
            if (($item['source'] ?? '') === 'invoice') {
                $balance += $item['amount'];
                $debit = $item['amount'];
                $credit = 0;
            } else {
                $balance -= $item['amount'];
                $debit = 0;
                $credit = $item['amount'];
            }

            $status = $balance > 0 ? '(عليك)' : ($balance < 0 ? '(لك)' : '—');

            $data[] = [
                $dateStr,
                $item['reference_id'],
                $item['note'],
                $debit > 0 ? $debit : '-',
                $credit > 0 ? $credit : '-',
                number_format(abs($balance), 2),
                $status
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'التاريخ / Date',
            'المرجع / Reference',
            'الوصف / Description',
            'المدين / Debit',
            'الدائن / Credit',
            'الرصيد / Balance',
            'الحالة / Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => 'center']],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 35,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}

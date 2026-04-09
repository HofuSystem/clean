<?php

namespace Core\B2B\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;

class TaxableInvoicesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // Check if it's an Invoice or B2BFinancial
        $isInvoice = isset($this->invoice_number);

        if ($isInvoice) {
            return [
                "id"             => $this->id,
                "order_ref"      => DashboardDataTableFormatter::text($this->order->reference_id ?? '—'),
                "invoice_number" => '<a href="' . route('dashboard.electronic-invoices.show', $this->id) . '" class="text-primary fw-bold" target="_blank">' . $this->invoice_number . '</a>',
                "customer"       => '<div class="fw-bold">' . ($this->order->company->fullname ?? $this->order->client->fullname ?? '—') . '</div>' .
                                    '<span class="badge ' . ($this->order->company_id ? 'bg-primary' : 'bg-secondary') . ' x-small" style="font-size: 0.6rem; padding: 0.2rem 0.5rem; border-radius: 99px;">' . ($this->order->company_id ? 'B2B' : 'B2C') . '</span>',
                "type"           => '<span class="badge ' . ($this->type === 'B2B' ? 'bg-primary' : 'bg-secondary') . '" style="padding: 0.25rem 0.75rem; border-radius: 99px;">' . $this->type . '</span>',
                "subtotal"       => number_format($this->subtotal, 2),
                "vat"            => '<span class="text-warning">' . number_format($this->vat_amount, 2) . '</span>',
                "total"          => '<div class="fw-bold">' . number_format($this->total, 2) . '</div>',
                "status"         => $this->order->status ? '<span class="badge bg-light-primary text-primary" style="padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.7rem; font-weight: bold;">' . trans($this->order->status) . '</span>' : '—',
                "receipt"        => DashboardDataTableFormatter::text($this->order->reference_id ?? '—'),
                "actions"        => '<a href="' . route('dashboard.electronic-invoices.show', $this->id) . '" target="_blank" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1" style="font-weight: 600; font-size: 0.75rem; padding: 0.4rem 0.8rem; border-radius: 0.5rem;">
                                        <i class="fas fa-eye"></i> ' . trans('tax preview') . '
                                     </a>',
                "source"         => "invoice"
            ];
        } else {
            // It's a B2BFinancial (Credit Note)
            return [
                "id"             => $this->id,
                "order_ref"      => DashboardDataTableFormatter::text($this->note ?: '—'),
                "invoice_number" => '<a href="' . route('dashboard.company-statement.print-credit-note', ['companyId' => $this->company_id, 'financialId' => $this->id]) . '" class="text-danger fw-bold" target="_blank">' . $this->reference_id . '</a>',
                "customer"       => '<div class="fw-bold">' . ($this->company->fullname ?? '—') . '</div>' .
                                    '<span class="badge bg-primary x-small" style="font-size: 0.6rem; padding: 0.2rem 0.5rem; border-radius: 99px;">B2B</span>',
                "type"           => '<span class="badge bg-danger" style="padding: 0.25rem 0.75rem; border-radius: 99px;">CREDIT</span>',
                "subtotal"       => number_format(-($this->amount / 1.15), 2),
                "vat"            => '<span class="text-danger">' . number_format(-($this->amount - ($this->amount / 1.15)), 2) . '</span>',
                "total"          => '<div class="fw-bold text-danger">' . number_format(-$this->amount, 2) . '</div>',
                "status"         => '—',
                "receipt"        => DashboardDataTableFormatter::text($this->reference_id),
                "actions"        => '<a href="' . route('dashboard.company-statement.print-credit-note', ['companyId' => $this->company_id, 'financialId' => $this->id]) . '" target="_blank" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-1" style="font-weight: 600; font-size: 0.75rem; padding: 0.4rem 0.8rem; border-radius: 0.5rem;">
                                        <i class="fas fa-eye"></i> ' . trans('tax preview') . '
                                     </a>',
                "source"         => "credit_note"
            ];
        }
    }
}

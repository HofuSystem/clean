<?php

namespace Core\Users\DataResources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $dateHtml = '-';
        if ($this->created_at) {
            $createdAt = Carbon::parse($this->created_at)->locale(app()->getLocale());
            $dateHtml = '<div class="d-flex flex-column text-start"><span class="text-dark fw-bold">'
                . e($createdAt->translatedFormat('d F Y')) . '</span><span class="text-muted fs-8">'
                . e($createdAt->translatedFormat('h:i A')) . '</span></div>';
        }

        $userHtml = '-';
        if ($this->user) {
            $userHtml = '<div class="d-flex flex-column text-start"><a class="fw-bold text-hover-primary" href="'
                . e(route('dashboard.users.show', $this->user->id)) . '">' . e($this->user->fullname ?: trans('points_ui.no_name'))
                . '</a><span class="text-muted fs-8">' . e($this->user->phone ?? '') . '</span></div>';
        }

        $operationLabel = $this->operation === 'deposit' ? trans('points_ui.points_added') : trans('points_ui.points_deducted');
        $operationColor = $this->operation === 'deposit' ? 'success' : 'danger';
        $operationHtml = '<span class="badge badge-light-' . $operationColor . '">'
            . e($operationLabel) . '</span>';

        $amountPrefix = $this->operation === 'deposit' ? '+' : '−';
        $amountColor = $this->operation === 'deposit' ? '#0bb783' : '#f1416c';
        $amountHtml = '<span class="fw-bolder" style="color:' . $amountColor . '; direction:ltr; display:inline-block">'
            . $amountPrefix . ' ' . e(number_format((float) $this->amount, 2)) . '</span>';

        $reason = $this->display_title;
        $reasonHtml = '<div class="d-flex flex-column text-start"><span class="text-dark fw-bold">'
            . e($reason) . '</span>';
        if ($this->expire_at) {
            $expiry = Carbon::parse($this->expire_at)->locale(app()->getLocale())->translatedFormat('d F Y');
            $reasonHtml .= '<span class="text-warning fs-8">' . e(trans('points_ui.expiry_date')) . ': ' . e($expiry) . '</span>';
        }
        $reasonHtml .= '</div>';

        $orderHtml = '-';
        if ($this->order) {
            $orderHtml = '<a class="badge bg-light text-primary border" href="'
                . e(route('dashboard.orders.show', $this->order->id)) . '">'
                . e($this->order->reference_id ?: ('#' . $this->order->id)) . '</a>';
        }

        $creatorHtml = $this->creator
            ? '<div class="d-flex flex-column text-start"><span class="text-dark">' . e($this->creator->fullname ?: trans('points_ui.no_name')) . '</span></div>'
            : '<span class="text-muted">' . e(trans('points_ui.automatic_unknown')) . '</span>';

        return [
            'id' => $this->id,
            'created_at' => $dateHtml,
            'user' => $userHtml,
            'details' => $reasonHtml,
            'operation' => $operationHtml,
            'amount' => $amountHtml,
            'current_balance' => $this->user ? number_format((float) $this->user->points_balance, 2) : '-',
            'order' => $orderHtml,
            'creator' => $creatorHtml,
            'actions' => $this->actions,
            'select_switch' => $this->select_switch,
        ];
    }
}

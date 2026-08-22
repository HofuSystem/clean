<?php
 
namespace Core\Wallet\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class WalletTransactionsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sar = trans('SAR');

        // Date and Time formatted
        $dateFormatted = '-';
        $timeFormatted = '';
        if ($this->created_at) {
            $carbon = Carbon::parse($this->created_at);
            $dateFormatted = $carbon->locale(app()->getLocale())->translatedFormat('d F Y');
            $timeFormatted = $carbon->locale(app()->getLocale())->translatedFormat('h:i A');
        }
        $createdAtHtml = '<div class="d-flex flex-column text-start">
            <span class="text-dark fw-bold fs-7">' . $dateFormatted . '</span>
            <span class="text-muted fs-8">' . $timeFormatted . '</span>
        </div>';

        // Customer info
        $userHtml = '-';
        if ($this->user) {
            $userUrl = route('dashboard.users.show', $this->user->id);
            $userHtml = '<div class="d-flex flex-column text-start">
                <a href="' . $userUrl . '" class="text-dark fw-bold text-hover-primary fs-7">' . e($this->user->fullname) . '</a>
                <span class="text-muted fs-8 dir-ltr text-start">' . e($this->user->phone ?? '') . '</span>
            </div>';
        }

        // Transaction Type Badge
        $txType = $this->transaction_type ?: ($this->type == 'withdraw' ? 'withdraw' : 'charge');
        $typeLabel = trans($txType);
        $badgeBg = '#f1f1f5';
        $badgeColor = '#5e6278';

        if (in_array($txType, ['charge', 'deposit']) || $this->package_id) {
            $typeLabel = $this->package_id ? trans('شحن عرض') : trans('شحن محفظة');
            $badgeBg = '#e8f4fd';
            $badgeColor = '#0d6efd';
        } elseif (in_array($txType, ['order_payment', 'withdraw'])) {
            $typeLabel = trans('دفع طلب');
            $badgeBg = '#f1f1f5';
            $badgeColor = '#5e6278';
        } elseif ($txType === 'remaining_amount') {
            $typeLabel = trans('استرداد');
            $badgeBg = '#fbeaf0';
            $badgeColor = '#d63384';
        } elseif ($txType === 'compensation_add') {
            $typeLabel = trans('تعويض');
            $badgeBg = '#e6f8f7';
            $badgeColor = '#0dcaf0';
        } elseif (in_array($txType, ['promotional_add', 'cashback', 'reward'])) {
            $typeLabel = trans('رصيد ترويجي');
            $badgeBg = '#fff4e6';
            $badgeColor = '#fd7e14';
        }

        $typeBadgeHtml = '<span class="badge" style="background-color:' . $badgeBg . '; color:' . $badgeColor . '; font-weight:600; padding:6px 12px; border-radius:6px; font-size:11px;">' . $typeLabel . '</span>';

        // Transaction Details
        $mainTitle = '';
        $subTitle = '';

        if ($this->package_id && $this->package) {
            $mainTitle = trans('شراء باقة رصيد') . ' ' . number_format($this->package->price, 0) . ' ' . $sar . ' (' . trans('عرض') . ')';
            $subTitle = ($this->bank_name ?: trans('بوابة الدفع'));
        } elseif ($this->order_id && $this->order) {
            if ($this->type == 'deposit' || $txType == 'remaining_amount') {
                $mainTitle = trans('إرجاع مبلغ طلب ملغي إلى المحفظة');
                $subTitle = trans('استرداد داخلي');
            } else {
                $mainTitle = trans('دفع قيمة الطلب من رصيد المحفظة');
                $subTitle = trans('خصم آلي');
            }
        } elseif ($txType === 'compensation_add') {
            $mainTitle = $this->notes ?: trans('تعويض خدمة');
            $subTitle = trans('إضافة إدارية');
        } elseif (in_array($txType, ['promotional_add', 'cashback', 'reward'])) {
            $mainTitle = $this->notes ?: trans('رصيد ترويجي');
            $subTitle = trans('إضافة إدارية');
        } else {
            $mainTitle = $this->notes ?: trans($txType);
            $subTitle = $this->bank_name ?: '';
        }

        $expiryHtml = '';
        if ($this->expired_at) {
            $expDate = Carbon::parse($this->expired_at)->locale(app()->getLocale())->translatedFormat('d F Y');
            $expiryHtml = '<span class="text-danger fs-8 d-block mt-1"><i class="far fa-clock text-danger me-1"></i> ' . trans('Expires on') . ': ' . $expDate . '</span>';
        }

        $detailsHtml = '<div class="d-flex flex-column text-start">
            <span class="text-dark fw-bold fs-7">' . e($mainTitle) . '</span>
            ' . ($subTitle ? '<span class="text-muted fs-8">' . e($subTitle) . '</span>' : '') . '
            ' . $expiryHtml . '
        </div>';

        // Net Amount (+ / -)
        $isPositive = ($this->type == 'deposit' || ($this->amount > 0 && $this->type != 'withdraw'));
        $amountFormatted = number_format(abs($this->amount), 2);
        if ($isPositive) {
            $amountHtml = '<span class="fw-bolder fs-6" style="color: #0bb783 !important; direction: ltr; display: inline-block;">+ ' . $amountFormatted . ' ' . $sar . '</span>';
        } else {
            $amountHtml = '<span class="fw-bolder fs-6" style="color: #f1416c !important; direction: ltr; display: inline-block;">- ' . $amountFormatted . ' ' . $sar . '</span>';
        }

        // Ending Balance (wallet_after and was: wallet_before)
        $walletAfter = number_format((float) $this->wallet_after, 2);
        $walletBefore = number_format((float) $this->wallet_before, 2);
        $endingBalanceHtml = '<div class="d-flex flex-column text-start">
            <span class="text-dark fw-bolder fs-7">' . $walletAfter . ' ' . $sar . '</span>
            <span class="text-muted fs-8">' . trans('was') . ': ' . $walletBefore . '</span>
        </div>';

        // Reference
        $refHtml = '-';
        if ($this->order_id && $this->order) {
            $orderUrl = route('dashboard.orders.show', $this->order->id);
            $refHtml = '<a href="' . $orderUrl . '" class="badge bg-light text-primary border px-2 py-1 fs-8 fw-bold">' . e($this->order->reference_id) . '</a>';
        } elseif ($this->transaction_id) {
            $refHtml = '<span class="badge bg-light text-dark border px-2 py-1 fs-8 fw-bold">' . e($this->transaction_id) . '</span>';
        } elseif ($this->package_id) {
            $refHtml = '<span class="badge bg-light text-secondary border px-2 py-1 fs-8">Offer #' . $this->package_id . '</span>';
        }

        // Added By
        $addedByTitle = $this->addedBy ? e($this->addedBy->fullname) : trans('System (Auto)');
        $addedBySub = $this->addedBy ? e($this->addedBy->email ?? '') : ($this->bank_name ?: trans('Payment Gateway'));
        $addedByHtml = '<div class="d-flex flex-column text-start">
            <span class="text-dark fw-bold fs-7">' . $addedByTitle . '</span>
            <span class="text-muted fs-8">' . $addedBySub . '</span>
        </div>';

        // Status
        $statusText = trans($this->status == 'accepted' ? 'Completed' : $this->status);
        $statusColor = ($this->status == 'accepted') ? '#0bb783' : (($this->status == 'pending') ? '#ffc107' : '#f1416c');
        $statusHtml = '<span class="fw-bold fs-7" style="color: ' . $statusColor . ';">' . $statusText . '</span>';

        // Actions
        $showUrl = route('dashboard.wallet-transactions.show', $this->id);
        $actionsHtml = '<div class="d-flex justify-content-center">
            <a href="' . $showUrl . '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="' . trans('Show') . '">
                <i class="fa fa-eye text-primary"></i>
            </a>
        </div>';

        return [
            "id"               => $this->id,
            "select_switch"    => '<div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input select-item-checkbox" type="checkbox" name="table_selected" value="' . $this->id . '"></div>',
            "created_at"       => $createdAtHtml,
            "user_id"          => $userHtml,
            "transaction_type" => $typeBadgeHtml,
            "details"          => $detailsHtml,
            "amount"           => $amountHtml,
            "wallet_after"     => $endingBalanceHtml,
            "reference"        => $refHtml,
            "added_by_id"      => $addedByHtml,
            "status"           => $statusHtml,
            "actions"          => $actionsHtml,
        ];
    }
}

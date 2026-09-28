<?php
 
namespace Core\Notification\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
use Core\Notification\Helpers\NotificationChannelResolver;

class NotificationsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $actions = $this->actions;
        if ($this->pending_count > 0) {
            $btn = '<a href="javascript:void(0)" class="btn-operation d-flex justify-content-center align-items-center mx-1 resend-pending-btn" data-id="'.$this->id.'" title="إعادة إرسال للمُعلقين ('.$this->pending_count.')"><i class="fas fa-sync"></i> <span>إعادة إرسال</span></a>';
            $actions = str_replace('</div>', $btn . '</div>', $actions);
        }

        // Status badge
        $status = $this->processing_status ?: 'completed';
        $statusBadges = [
            'completed' => '<span class="badge bg-label-success badge-light-success">مكتمل</span>',
            'processing' => '<span class="badge bg-label-primary badge-light-primary"><i class="fas fa-spinner fa-spin me-1"></i>جاري الإرسال</span>',
            'queued' => '<span class="badge bg-label-warning badge-light-warning">في الطابور</span>',
            'failed' => '<span class="badge bg-label-danger badge-light-danger">فشل</span>',
            'draft' => '<span class="badge bg-label-secondary badge-light-secondary">مسودة</span>',
        ];
        $statusHtml = $statusBadges[$status] ?? '<span class="badge bg-label-secondary badge-light-secondary">' . $status . '</span>';

        // 1. Resolve Channel
        $resolvedChannel = NotificationChannelResolver::resolveChannel($this->resource);
        $channelBadge = NotificationChannelResolver::renderChannelBadge($resolvedChannel);
        $channelLabel = NotificationChannelResolver::getChannelLabel($resolvedChannel);

        // 2. Resolve Purpose (Never default to marketing!)
        $resolvedPurpose = NotificationChannelResolver::resolvePurpose($this->resource);
        $purposeBadge = NotificationChannelResolver::renderPurposeBadge($resolvedPurpose);
        $purposeLabel = NotificationChannelResolver::getPurposeLabel($resolvedPurpose);

        // 3. Resolve Transport Type & Delivery Semantics
        $isFcm = NotificationChannelResolver::isFcm($this->resource);
        $deliveryChannel = method_exists($this->resource, 'getDeliveryChannel') ? $this->getDeliveryChannel() : ($isFcm ? 'legacy_topic' : $resolvedChannel);
        $isDirect = ($deliveryChannel === 'direct_fcm');
        $transportType = NotificationChannelResolver::resolveTransportType($resolvedChannel, $deliveryChannel);
        $combinedLabel = NotificationChannelResolver::formatCombinedLabel($this->resource);

        if ($isFcm) {
            if ($isDirect) {
                $acceptedDisplay = '<span class="badge bg-label-success badge-light-success fs-7 fw-bold" title="Direct FCM (Push)">' .
                    trans('قُبل من FCM') . ': ' . number_format($this->accepted_by_fcm_count ?? 0) . '</span>';
            } else {
                $legacyCount = (int) ($this->sent_count ?? 0);
                $displayCount = $legacyCount > 0 ? number_format($legacyCount) : '—';
                $acceptedDisplay = '<span class="badge bg-label-primary badge-light-primary fs-7" title="قبول من نظام Topic القديم — ليس دليلاً على وصول الإشعار للجهاز أو عرضه أو فتحه">' .
                    trans('قبول اشتراك الموضوع (Legacy Topic Subscription)') . ': ' . $displayCount . '</span>';
            }
        } elseif ($resolvedChannel === NotificationChannelResolver::CHANNEL_WHATSAPP) {
            $sentVal = $this->sent_count ? number_format($this->sent_count) : 'تم الإرسال';
            $acceptedDisplay = '<span class="badge bg-label-success badge-light-success fs-7 fw-bold" title="WhatsApp Gateway API"><i class="fab fa-whatsapp me-1"></i>بوابة WhatsApp: ' . $sentVal . '</span>';
        } elseif ($resolvedChannel === NotificationChannelResolver::CHANNEL_SMS) {
            $sentVal = $this->sent_count ? number_format($this->sent_count) : 'تم الإرسال';
            $acceptedDisplay = '<span class="badge bg-label-info badge-light-info fs-7 fw-bold" title="SMS Gateway API"><i class="fas fa-sms me-1"></i>بوابة SMS: ' . $sentVal . '</span>';
        } elseif ($resolvedChannel === NotificationChannelResolver::CHANNEL_EMAIL) {
            $sentVal = $this->sent_count ? number_format($this->sent_count) : 'تم الإرسال';
            $acceptedDisplay = '<span class="badge bg-label-warning badge-light-warning fs-7 fw-bold" title="SMTP Mail Server"><i class="fas fa-envelope me-1"></i>خادم Email: ' . $sentVal . '</span>';
        } else {
            $acceptedDisplay = '<span class="badge bg-label-secondary badge-light-secondary fs-7">غير مصنف</span>';
        }

        $targetedCountFormatted = $this->targeted_users_count ? number_format($this->targeted_users_count) : ($this->for === 'all' ? trans('All Users') : number_format($this->users()->count()));

        $data = [
            "id"                        => $this->id,
            "types"                     => DashboardDataTableFormatter::text($this->types),
            "channel"                   => $channelBadge,
            "channel_raw"               => $resolvedChannel,
            "channel_label"             => $channelLabel,
            "for"                       => DashboardDataTableFormatter::text($this->for),
            "purpose"                   => $purposeBadge,
            "purpose_raw"               => $resolvedPurpose,
            "purpose_label"             => $purposeLabel,
            "transport_type"            => $transportType,
            "combined_label"            => $combinedLabel,
            "processing_status"         => $statusHtml,
            "delivery_channel"          => $deliveryChannel,
            "delivery_channel_label"    => ($isDirect ? 'Direct FCM' : ($deliveryChannel === 'legacy_topic' ? 'Legacy Topic Subscription' : $transportType)),
            "delivery_metric_label"     => method_exists($this->resource, 'getDeliveryMetricLabel') ? $this->getDeliveryMetricLabel() : $transportType,
            "accepted_by_fcm"           => ($isFcm && $isDirect) ? (int) ($this->accepted_by_fcm_count ?? 0) : '—',
            "legacy_topic_count"        => ($isFcm && !$isDirect) ? (int) ($this->sent_count ?? 0) : '—',
            "targeted_users_count"      => $targetedCountFormatted,
            "eligible_devices_count"    => $isFcm ? ($this->eligible_devices_count !== null ? number_format($this->eligible_devices_count) : '—') : '—',
            "fcm_users_count"           => $isFcm ? $targetedCountFormatted : trans('Not applicable'),
            "title"                     => DashboardDataTableFormatter::text($this->title),
            "body"                      => DashboardDataTableFormatter::text($this->body),
            "media"                     => DashboardDataTableFormatter::mediaCenter($this->media),
            "sender_id"                 => DashboardDataTableFormatter::relations($this->sender, "fullname", "dashboard.users.show"),
            "sent_count"                => $acceptedDisplay,
            "created_at"                => $this->created_at?->format('Y-m-d h:i a'),
            "actions"                   => $actions,
            "select_switch"             => $this->select_switch,
        ];

        $for = $this->for;
        if ($for == "users") {
            $count = $this->users()->count();
            $users = $this->users()->first();
            if ($count == 1 && $users) {
                $for = "<a href='".route('dashboard.users.edit', $users->id)."'>".$users->phone." - ".$users->fullname."</a>";
            } else {
                $for = $count . " " . trans('users');
            }
        }
        $data['for'] = $for;

        return $data;
    }
}
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
            'completed' => '<span class="badge bg-label-success badge-light-success"><i class="fas fa-check-circle me-1"></i>مكتمل</span>',
            'processing' => '<span class="badge bg-label-info badge-light-info"><i class="fas fa-spinner fa-spin me-1"></i>جاري الإرسال</span>',
            'queued' => '<span class="badge bg-label-warning badge-light-warning"><i class="fas fa-clock me-1"></i>في الطابور</span>',
            'failed' => '<span class="badge bg-label-danger badge-light-danger"><i class="fas fa-times-circle me-1"></i>فشل</span>',
            'draft' => '<span class="badge bg-label-secondary badge-light-secondary"><i class="fas fa-file-alt me-1"></i>مسودة</span>',
        ];
        $statusHtml = $statusBadges[$status] ?? '<span class="badge bg-label-secondary badge-light-secondary"><i class="fas fa-info-circle me-1"></i>' . $status . '</span>';

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
                $acceptedDisplay = '<span class="badge bg-label-success badge-light-success fs-7 fw-bold" title="Direct FCM (Push)"><i class="fas fa-bolt me-1"></i>' .
                    trans('قُبل من FCM') . ': ' . number_format($this->accepted_by_fcm_count ?? 0) . '</span>';
            } else {
                $rawCount = $this->sent_count;
                if ($rawCount !== null && is_numeric($rawCount)) {
                    $intCount = (int) $rawCount;
                    if ($intCount > 0) {
                        $acceptedDisplay = '<span class="badge bg-label-primary badge-light-primary fs-7" data-metric="Legacy Topic Subscription" title="هذا يعني أن FCM القديم قبل طلب الإرسال فقط، ولا يثبت وصول الإشعار أو فتحه."><i class="fas fa-layer-group me-1"></i>قبول الإرسال القديم: ' . number_format($intCount) . '</span>';
                    } else {
                        $acceptedDisplay = '<span class="badge bg-label-secondary badge-light-secondary fs-7" data-metric="Legacy Topic Subscription" title="لم يتم قبول أي إرسال عبر المسار القديم."><i class="fas fa-layer-group me-1"></i>قبول الإرسال القديم: 0</span>';
                    }
                } else {
                    $acceptedDisplay = '<span class="badge bg-label-secondary badge-light-secondary fs-7" data-metric="Legacy Topic Subscription" title="هذا الإشعار لا يستخدم مسار Legacy Topic أو لا يتوفر له هذا القياس."><i class="fas fa-minus-circle me-1"></i>غير منطبق</span>';
                }
            }
        } elseif (in_array($resolvedChannel, [
            NotificationChannelResolver::CHANNEL_WHATSAPP,
            NotificationChannelResolver::CHANNEL_SMS,
            NotificationChannelResolver::CHANNEL_EMAIL,
            NotificationChannelResolver::CHANNEL_IN_APP,
        ], true) || !$isFcm) {
            $acceptedDisplay = '<span class="badge bg-label-secondary badge-light-secondary fs-7" data-metric="Legacy Topic Subscription" title="هذا الإشعار لا يستخدم مسار Legacy Topic أو لا يتوفر له هذا القياس."><i class="fas fa-minus-circle me-1"></i>لا ينطبق — قناة WhatsApp/SMS/Email</span>';
        } else {
            $acceptedDisplay = '<span class="badge bg-label-secondary badge-light-secondary fs-7"><i class="fas fa-question-circle me-1"></i>غير مصنف</span>';
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
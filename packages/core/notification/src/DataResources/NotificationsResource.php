<?php
 
namespace Core\Notification\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;

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

        // Purpose badge
        $purpose = $this->purpose ?: 'marketing';
        $purposeBadges = [
            'marketing' => '<span class="badge bg-label-info badge-light-info">تسويقي</span>',
            'transactional' => '<span class="badge bg-label-primary badge-light-primary">تشغيلي</span>',
            'system' => '<span class="badge bg-label-dark badge-light-dark">نظام</span>',
        ];
        $purposeHtml = $purposeBadges[$purpose] ?? '<span class="badge bg-label-info badge-light-info">' . $purpose . '</span>';

        // Delivery channel & semantics differentiation
        $channel = method_exists($this->resource, 'getDeliveryChannel') ? $this->getDeliveryChannel() : 'legacy_topic';
        $isDirect = ($channel === 'direct_fcm');
        $newMetricsEnabled = config('notification.new_dashboard_metrics_enabled', true);

        if ($newMetricsEnabled) {
            if ($isDirect) {
                // Direct FCM campaign
                $acceptedDisplay = '<span class="badge bg-label-success badge-light-success fs-7 fw-bold" title="Direct FCM">' .
                    trans('قُبل من FCM') . ': ' . number_format($this->accepted_by_fcm_count ?? 0) . '</span>';
            } else {
                // Legacy campaign: explicit Legacy Topic Subscription semantics
                $legacyCount = (int) ($this->sent_count ?? 0);
                $displayCount = $legacyCount > 0 ? number_format($legacyCount) : '—';
                $acceptedDisplay = '<span class="badge bg-label-primary badge-light-primary fs-7" title="Legacy Topic Subscription">' .
                    trans('Legacy Topic Subscription') . ': ' . $displayCount . '</span>';
            }
        } else {
            $acceptedDisplay = '<span class="badge bg-label-secondary badge-light-secondary fs-7">' . number_format($this->sent_count ?? 0) . '</span>';
        }

        $data = [
            "id"                        => $this->id,
            "types"                     => DashboardDataTableFormatter::text($this->types),
            "for"                       => DashboardDataTableFormatter::text($this->for),
            "purpose"                   => $purposeHtml,
            "processing_status"         => $statusHtml,
            "delivery_channel"          => $channel,
            "delivery_channel_label"    => $isDirect ? 'Direct FCM' : 'Legacy Topic Subscription',
            "delivery_metric_label"     => $isDirect ? trans('قُبل من FCM') : trans('Legacy Topic Subscription'),
            "accepted_by_fcm"           => $isDirect ? (int) ($this->accepted_by_fcm_count ?? 0) : '—',
            "legacy_topic_count"        => !$isDirect ? (int) ($this->sent_count ?? 0) : '—',
            "targeted_users_count"      => $this->targeted_users_count ? number_format($this->targeted_users_count) : ($this->for === 'all' ? trans('All Users') : number_format($this->users()->count())),
            "eligible_devices_count"    => $this->eligible_devices_count ? number_format($this->eligible_devices_count) : '—',
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

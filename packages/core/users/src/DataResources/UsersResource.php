<?php
 
namespace Core\Users\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
use Core\Orders\Helpers\OrderHelper;
use Core\Settings\Helpers\ToolHelper;

class UsersResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data =  [
            "id"              => $this->id,
            "image"           => DashboardDataTableFormatter::mediaCenter($this->image),
            "fullname"        => DashboardDataTableFormatter::text($this->fullname),
            "email"           => DashboardDataTableFormatter::text($this->email),
            "phone"           => DashboardDataTableFormatter::text($this->phone),
            "roles"           => DashboardDataTableFormatter::relations($this->roles,"name","dashboard.roles.show"),
            "is_active"       => DashboardDataTableFormatter::checkbox($this->is_active),
            "is_allow_notify" => DashboardDataTableFormatter::checkbox($this->is_allow_notify),
            "orders_count"    => DashboardDataTableFormatter::text($this->orders_count),
            "gender"          => DashboardDataTableFormatter::text($this->gender ? trans($this->gender) : null),
            "city"            => $this?->profile?->city?->name,
            "district"        => $this?->profile?->district?->name,
            "created_at"      => $this->created_at?->format('Y-m-d H:i:a'),
            "latest_order_at"   => $this->latest_order_at,
            "actions"         => $this->actions,
            "select_switch"   => $this->select_switch,
            "showActions"     => $this->show_actions,
            "sent_status"     => $this->sent_status ?? $this->pivot?->status,
        ];
        
        $sent_response = $this->sent_response ?? $this->pivot?->response;
        if ($sent_response) {
            $responses = is_string($sent_response) ? json_decode($sent_response, true) : $sent_response;
            if (is_array($responses)) {
                $translatedResponses = array_map(function($r) {
                    if (is_array($r) || is_object($r)) {
                        return json_encode($r, JSON_UNESCAPED_UNICODE);
                    }
                    $string_r = trim((string)$r, '"[]');
                    if ($string_r === 'No device token') return 'لا يوجد توكن للجهاز';
                    return trans($string_r); 
                }, $responses);
                $data['sent_response'] = implode(', ', $translatedResponses);
            } else {
                $string_response = trim((string)$sent_response, '"[]');
                if ($string_response === 'No device token') {
                    $data['sent_response'] = 'لا يوجد توكن للجهاز';
                } else {
                    $data['sent_response'] = trans($string_response);
                }
            }
        } else {
            $data['sent_response'] = null;
        }

        $customerTire         = OrderHelper::getCustomerTier($this->orders_count);
        $class                = trans($customerTire['type']);
        $color                = $customerTire['color'];
        $data['class']        =  '<span class="ms-2 p-2 rounded" style="background-color:'.$color.'; color:#fff">'.$class.'</span>';
        
        if($data['sent_status'] == 'sent'){
            $data['sent_status'] = '<span class="badge bg-success">تم الإرسال</span>';
        }elseif($data['sent_status'] == 'failed'){
            $data['sent_status'] = '<span class="badge bg-danger">فشل الإرسال</span>';
        }elseif($data['sent_status'] == 'pending'){
            $data['sent_status'] = '<span class="badge bg-warning">قيد الانتظار</span>';
            $notificationId = request()->route('id');
            if ($notificationId) {
                $btn = '<a href="javascript:void(0)" class="btn-operation d-flex justify-content-center align-items-center mx-1 resend-user-btn" data-id="'.$this->id.'" data-notification-id="'.$notificationId.'" title="إعادة إرسال"><i class="fas fa-sync"></i> <span>إرسال</span></a>';
                $data['showActions'] = str_replace('</div>', $btn . '</div>', $data['showActions']);
            }
        }

        return $data;
    }
}

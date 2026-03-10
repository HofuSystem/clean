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
            "gender"          => DashboardDataTableFormatter::text($this->gender),
            "city"            => $this?->profile?->city?->name,
            "district"        => $this?->profile?->district?->name,
            "created_at"      => $this->created_at?->format('Y-m-d H:i:a'),
            "latest_order_at"   => $this->latest_order_at,
            "actions"         => $this->actions,
            "select_switch"   => $this->select_switch,
            "showActions"     => $this->show_actions,
            "sent_status"     => $this->sent_status,
            "sent_response"   => $this->sent_response,
        ];
        $customerTire         = OrderHelper::getCustomerTier($this->orders_count);
        $class                = trans($customerTire['type']);
        $color                = $customerTire['color'];
        $data['class']        =  '<span class="ms-2 p-2 rounded" style="background-color:'.$color.'; color:#fff">'.$class.'</span>';
        if($data['sent_status'] == 'sent'){
            $data['sent_status'] = '<span class="badge bg-success">sent</span>';
        }elseif($data['sent_status'] == 'failed'){
            $data['sent_status'] = '<span class="badge bg-danger">failed</span>';
        }else{
            $data['sent_status'] = '<span class="badge bg-warning">pending</span>';
        }

        return $data;
    }
}

<?php

namespace Core\Orders\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
use Core\Orders\Models\Order;
use Core\Products\Models\Product;
use Core\Settings\Helpers\ToolHelper;

class CartsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = json_decode($this->data, true);
        $data = ToolHelper::isJson($data) ? json_decode($data, true) : $data;
        $data = is_array($data) ? $data : [];
        $productIds = array_filter(array_map(fn($product) => is_array($product) ? ($product['id'] ?? null) : null, $data));
        $products = !empty($productIds) ? Product::whereIn('id', $productIds)->get()->keyBy('id') : collect();

        $totalPrice = 0;
        foreach ($data as $item) {
            if (is_array($item) && isset($item['id'], $products[$item['id']])) {
                $totalPrice += ($products[$item['id']]->price ?? 0) * ($item['quantity'] ?? 1);
            }
        }
        return [

            'id'                   =>  $this->id,
            'user_id'              =>  DashboardDataTableFormatter::relation($this->user, 'fullname', 'dashboard.users.edit'),
            'phone'                =>  $this->phone,
            'city'                 =>  $this->user?->profile?->city?->name,
            'district'             =>  $this->user?->profile?->other_city_name ?? $this->user?->profile?->district?->name,
            'number_of_items'      =>  count($data),
            'number_of_orders'     =>  $this->user?->orders?->count() ?? 0,
            'last_order'           =>  $this->user?->orders?->sortByDesc('created_at')->first()?->created_at?->format("Y-m-d"),
            'order_total_price'    =>  $totalPrice,
            'follow_up_count'      =>  $this->followUps?->count() ?? 0,
            'has_active_follow_up' =>  $this->activeFollowUp
                                        ? '<span class="badge badge-success">'.trans('active').'</span>'
                                        : '<span class="badge badge-secondary">'.trans('none').'</span>',
            'created_at'           =>  $this->created_at?->format("Y-m-d"),
            'updated_at'           =>  $this->updated_at?->format("Y-m-d"),
            "actions"              =>  $this->actions,
            "select_switch"        =>  $this->select_switch,
        ];
    }
}

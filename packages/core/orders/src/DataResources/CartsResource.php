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
        $data = json_decode($this->data,true);
        $data = ToolHelper::isJson($data) ? json_decode($data,true) : $data;
        $productIds = array_map(fn($product) => $product['id'], $data);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $totalPrice = 0;
        foreach ($data ?? [] as $item) {
            if($products[$item['id']]){
                $totalPrice += $products[$item['id']]->price * $item['quantity'];
            }
            
        }
        return [

            'id'                =>  $this->id,
            'user_id'           =>  DashboardDataTableFormatter::relation($this->user,'fullname','dashboard.users.edit'),
            'phone'             =>  $this->phone,
            'city'              =>  $this->user?->profile?->city?->name,
            'district'          =>  $this->user?->profile?->other_city_name ?? $this->user?->profile?->district?->name,
            'number_of_items'  =>   count($data),
            'number_of_orders'  =>  $this->user?->orders->count(),
            'last_order'        =>  $this->user?->orders()->latest()->first()?->created_at?->format("Y-m-d"),
            'order_total_price' =>  $totalPrice,
            'created_at'        =>  $this->created_at?->format("Y-m-d"),
            'updated_at'        =>  $this->updated_at?->format("Y-m-d"),
            "actions"           =>  $this->actions,
            "select_switch"     =>  $this->select_switch,
        ];
    }
}

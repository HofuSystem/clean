<?php

namespace Core\Products\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
use Core\Settings\Helpers\ToolHelper;
class ProductsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [

            "id"                => $this->id,
            "name"              => DashboardDataTableFormatter::text($this->name),
            "image"             => DashboardDataTableFormatter::mediaCenter($this->image),
            "type"              => DashboardDataTableFormatter::text($this->type),
            "sku"               => DashboardDataTableFormatter::text($this->sku),
            "category_id"       => DashboardDataTableFormatter::relations($this->category,"name","dashboard.categories.show"),
            "sub_category_id"   => DashboardDataTableFormatter::relations($this->subCategory,"name","dashboard.categories.show"),
            "price"             => ToolHelper::getPriceBasedOnCurrentWeekDay($this->price),
            "cost"              => DashboardDataTableFormatter::text($this->cost),
            "quantity"          => DashboardDataTableFormatter::text($this->quantity),
            "status"            => DashboardDataTableFormatter::text($this->status),
            "actions"           => $this->actions,
            "select_switch"     => $this->select_switch,
            'created_at'        =>$this->created_at->format('Y-m-d'),
            'total_quantity'   =>$this->total_quantity,
        ];
    }
}

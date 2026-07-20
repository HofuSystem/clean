<?php
 
namespace Core\Products\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;

class ProductSettingsResource extends JsonResource
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
            "slug"              => DashboardDataTableFormatter::text($this->slug),
            "name"              => DashboardDataTableFormatter::text($this->name),
            "product_id"        => DashboardDataTableFormatter::relations($this->products, "name", "dashboard.products.show"),
            "addon_price"       => DashboardDataTableFormatter::text($this->addon_price),
            "discount_percent"  => DashboardDataTableFormatter::text($this->discount_percent),
            "cost"              => DashboardDataTableFormatter::text($this->cost),
            "parent_id"         => DashboardDataTableFormatter::relations($this->parent, "name", "dashboard.product-settings.show"),
            "status"            => DashboardDataTableFormatter::text(trans($this->status)),
            "general"           => DashboardDataTableFormatter::text($this->general ? trans('yes') : trans('no')),
            "color"             => $this->color ? '<div style="width: 15px; height: 15px; border-radius: 50%; background-color: ' . $this->color . '; display: inline-block; vertical-align: middle; border: 1px solid #ddd; margin-right: 5px;"></div> <span>' . $this->color . '</span>' : '--',
            "icon"              => DashboardDataTableFormatter::mediaCenter($this->icon),
            "product_settings"  => DashboardDataTableFormatter::relations($this->productSettings, "name", "dashboard.product-settings.show"),
            "actions"           => $this->actions,
            "select_switch"     => $this->select_switch,
        ];
    }
}

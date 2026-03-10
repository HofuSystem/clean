<?php
 
namespace Core\Users\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class ContractsPricesResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            
            "id"            => $this->id,
            "contract_id"   => DashboardDataTableFormatter::relations($this->contract,"title","dashboard.contracts.show"),
            "product_id"    => DashboardDataTableFormatter::relations($this->product,"name","dashboard.products.show"),
            "price"         => DashboardDataTableFormatter::text($this->price),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,
           
        ];
    }
}

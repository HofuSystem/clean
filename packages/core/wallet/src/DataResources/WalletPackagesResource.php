<?php

namespace Core\Wallet\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class WalletPackagesResource extends JsonResource
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
            "image"         => DashboardDataTableFormatter::mediaCenter($this->image),
            "price"         => DashboardDataTableFormatter::text($this->price),
            "value"         => DashboardDataTableFormatter::text($this->value),
            "status"        => DashboardDataTableFormatter::text($this->status),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,

        ];
    }
}

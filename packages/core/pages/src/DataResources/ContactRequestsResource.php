<?php

namespace Core\Pages\DataResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class ContactRequestsResource extends JsonResource
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
            "name"          => DashboardDataTableFormatter::text($this->name),
            "phone"         => DashboardDataTableFormatter::text($this->phone),
            "email"         => DashboardDataTableFormatter::text($this->email),
            "type"          => DashboardDataTableFormatter::text($this->type),
            "notes"         => DashboardDataTableFormatter::text($this->notes),
            "actions"       => $this->actions,
            "select_switch" => $this->select_switch,

        ];
    }
}

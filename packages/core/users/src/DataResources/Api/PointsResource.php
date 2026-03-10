<?php

namespace Core\Users\DataResources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class PointsResource extends JsonResource
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
            "title"         => $this->title,
            "amount"        => (int)$this->amount,
            "operation"     => $this->operation,
            "add_date"      => $this->created_at->format('d-F-Y'),
            'expire_at'     => $this->expire_at,
        ];
    }
}

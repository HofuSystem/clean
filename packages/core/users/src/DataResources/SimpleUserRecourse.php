<?php

namespace Core\Users\DataResources;

use Core\Admin\Helpers\DashboardDataTableFormatter;
use Core\Info\DataResources\CityResource;
use Core\Info\DataResources\DistrictResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleUserRecourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'fullname'   => $this->fullname,
            'user_type'  => $this->roles->first()?->name,
        ];
    }
}

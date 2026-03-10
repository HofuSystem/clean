<?php

namespace Core\MediaCenter\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Core\MediaCenter\Models\MediaCenter;

class MediaCenterResource extends JsonResource
{
    public function toArray($request)
    {
        return \Core\MediaCenter\Helpers\MediaCenterHelper::getInstance()->mediaCenterResponse(json_decode(json_encode($this) ,true));
    }
}

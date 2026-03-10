<?php

namespace Core\MediaCenter\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Core\MediaCenter\Models\MediaCenter;

class MediaCenterArrayResource extends JsonResource
{
    public function toArray($request)
    {
        $data = (isset($this->image_list) && !empty($this->image_list) ? json_decode($this->image_list)->thumbnail : '');
        return ([
            'id' => $this->id,
            'type' => $this->file_type,
            'title' => $this->title,
            'file' => $this->url . $this->file_name,
            'file_path_list' => $this->url,
            'image_list' => $data,
        ]);
    }
}

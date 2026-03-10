<?php

namespace Core\Categories\DataResources\Api\Services;

use Core\Categories\Models\Category;
use Core\Users\Models\Fav;
use Illuminate\Http\Resources\Json\JsonResource;

class CareHostServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id ,
            'name'              => $this->name ,
            //'hour_price'        => $this->hour_price,
            //'sale_price'        => $this->sale_price,
            'is_fav'            => Fav::where('favs_type',Category::class)->where('favs_id',$this->id)->where('user_id',auth('api')->user()?->id)->exists(),
            'sub_service'       => SubServiceResource::collection($this->subCategories),


        ];
    }
}

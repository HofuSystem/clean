<?php

namespace Core\Products\DataResources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class TestProductResource extends JsonResource
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
            'id'                => 0 ,
            'name'              => 'تجريبى' ,
            'image'             => '' ,
            'price'             => 0.00 ,
            'desc'              => ''  ,
            'available_quantity'=> 1  ,
            'is_fav'            => false ,
        ];
    }
}

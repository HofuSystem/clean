<?php

namespace Core\Categories\DataResources\Api\Services;

use Carbon\Carbon;
use Core\Categories\DataResources\Api\Services\ServiceTimeResource;
use Core\Categories\Models\CategoryDateTime;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceDateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $times = CategoryDateTime::where('date',$this->date)->where('category_id',$this->category_id)->get();
        return [
            'id'    =>  $this->id ,
            'day'   =>  strtolower(Carbon::parse($this->date)->format('l')),
            'date'  =>  Carbon::parse($this->date)->format('Y-m-d') ,
            'times' =>  ServiceTimeResource::collection($times),
        ];
    }
}

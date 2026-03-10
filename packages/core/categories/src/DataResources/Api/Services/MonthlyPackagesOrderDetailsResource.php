<?php

namespace Core\Categories\DataResources\Api\Services;

use Core\Categories\Models\CategorySetting;
use Core\Workers\DataResources\Api\WorkerResource;
use Core\Workers\Models\Worker;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyPackagesOrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $workers            = Worker::where('status','active')->get();
        $contractDuration   = CategorySetting::whereHas('parent',function($parentQuery){
            $parentQuery->where('slug','contract duration');
        })
        ->active()
        ->get();
        return [
            'id'                => $this->id ,
            'name'              => $this->name ,
            'workers'           => WorkerResource::collection($workers),
            'contract_duration' => ChildServiceSettingResource::collection($contractDuration)
        ];
    }
}

<?php

namespace Core\Categories\DataResources\Api\Services;

use Core\Categories\Models\CategoryDateTime;
use Core\Categories\Models\CategorySetting;
use Core\Categories\Services\CategoryDateTimesService;
use Illuminate\Http\Resources\Json\JsonResource;

class HostOrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
   
        $uniform       = CategorySetting::whereHas('parent',function($parentQuery){
            $parentQuery->where('slug','uniform');
        })
        ->active()
        ->get();
        
        $workers_num  = CategorySetting::whereHas('parent',function($parentQuery){
            $parentQuery->where('slug','number-of-workers');
        })
        ->active()
        ->get();
        
        $period       = CategorySetting::whereHas('parent',function($parentQuery){
            $parentQuery->where('slug','period');
        })
        ->active()
        ->get();
        
        $serviceDates = CategoryDateTimesService::getDateTimes(type: 'host',categoryIds: $this->id);
        $serviceDates = CategoryDateTimesService::getDateTimesFormatted('delivery', $serviceDates);

        return [
            'id'                => $this->id ,
            'name'              => $this->name ,
            'host_types'        => ServiceTypeResource::collection($this->categoryTypes),
            'uniform'           => ChildServiceSettingResource::collection($uniform),
            'workers_num'       => ChildServiceSettingResource::collection($workers_num),
            'period'            => ChildServiceSettingResource::collection($period),
            'dates'             => $serviceDates,
        ];
    }
}

<?php

namespace Core\Categories\DataResources\Api\Services;

use Core\Categories\Models\CategoryDateTime;
use Core\Categories\Models\CategorySetting;
use Core\Categories\Services\CategoryDateTimesService;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduledOrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $contractDuration   = CategorySetting::whereHas('parent',function($parentQuery){
            $parentQuery->where('slug','contract-duration');
        })
        ->active()
        ->get();
        $workersNumber = CategorySetting::whereHas('parent',function($parentQuery){
            $parentQuery->where('slug','workers-number');
        })
        ->active()
        ->get();

        $hoursNumber   = CategorySetting::whereHas('parent',function($parentQuery){
            $parentQuery->where('slug','number-of-service-hours');
        })
        ->active()
        ->get();

        $nationalities   = CategorySetting::whereHas('parent',function($parentQuery){
            $parentQuery->where('slug','nationalities');
        })
        ->active()
        ->get();
        
        $serviceDates = CategoryDateTimesService::getDateTimes(type: 'maid',categoryIds: $this->id);
        $serviceDates = CategoryDateTimesService::getDateTimesFormatted('receiver', $serviceDates);


        return [
            'id'                => $this->id ,
            'name'              => $this->name ,
            'contract_duration' => ChildServiceSettingResource::collection($contractDuration),
            'nationalities'     => ChildServiceSettingResource::collection($nationalities),
            'workers_number'    => ChildServiceSettingResource::collection($workersNumber),
            'hours_number'      => ChildServiceSettingResource::collection($hoursNumber),
            'dates'             => $serviceDates,
        ];
    }
}

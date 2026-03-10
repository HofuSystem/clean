<?php
namespace Core\Categories\DataResources\Api\Services;

use Core\Categories\Models\CategoryDateTime;
use Core\Categories\Models\CategorySetting;
use Core\Categories\Services\CategoryDateTimesService;
use Illuminate\Http\Resources\Json\JsonResource;

class SelfCareOrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $additionalFeatures = CategorySetting::whereHas('category',function($categoryQuery){
            $categoryQuery->where('slug','selfcare-service');
        })
        ->where('addon_price','!=',null)
        ->whereNull('parent_id')
        ->active()
        ->get();

        $serviceDuration = CategorySetting::whereHas('parent',function($parentQuery){
            $parentQuery->where('slug','service-duration');
        })
        ->active()
        ->get();
        
        $serviceDates  = CategoryDateTimesService::getDateTimes(type: 'host',categoryIds: $this->id);
        $serviceDates = CategoryDateTimesService::getDateTimesFormatted('delivery', $serviceDates);

        return [
            'id'                    => $this->id ,
            'name'                  => $this->name ,
            'self_care_types'       => ServiceTypeResource::collection($this->categoryTypes),
            'service_duration'      => ChildServiceSettingResource::collection($serviceDuration),
            'dates'                 => $serviceDates,
            'additional_features'   => ServiceSettingResource::collection($additionalFeatures),
        ];
    }
}

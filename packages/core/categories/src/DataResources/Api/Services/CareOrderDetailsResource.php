<?php

namespace Core\Categories\DataResources\Api\Services;

use Core\Categories\Models\CategorySetting;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class CareOrderDetailsResource extends JsonResource
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
            $categoryQuery->where('slug','care-service');
        })
        ->where('addon_price','!=',null)
        ->whereNull('parent_id')
        ->active()
        ->get();

        $careDuration  = CategorySetting::whereHas('parent',function($parentQuery){
            $parentQuery->where('slug','care-duration');
        })
        ->active()
        ->get();

        $period         = CategorySetting::whereHas('parent',function($parentQuery){
            $parentQuery->where('slug','period');
        })
        ->active()
        ->get();

        return [
            'id'                    => $this->id ,
            'name'                  => $this->name ,
            'care_types'            => ServiceTypeResource::collection($this->categoryTypes),
            //'care_type'             => SubServiceResource::collection($this->childs),
            'care_duration'         => ChildServiceSettingResource::collection($careDuration),
            'period'                => ChildServiceSettingResource::collection($period),
            'additional_features'   => ServiceSettingResource::collection($additionalFeatures),
        ];
    }
}

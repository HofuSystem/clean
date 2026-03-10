<?php

namespace Core\Info\DataResources;

use Core\Info\Models\CityTranslation;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //city_id
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_ar' => CityTranslation::where('locale','ar')->where('city_id',$this->id)->first()->name,
            'name_en' => CityTranslation::where('locale','en')->where('city_id',$this->id)->first()->name,
            'image' => MediaCenterHelper::getImagesUrl($this->image),
            'status' => $this->status,
        ];
    }
}

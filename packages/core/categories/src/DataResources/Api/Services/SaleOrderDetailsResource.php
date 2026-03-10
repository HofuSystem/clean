<?php

namespace Core\Categories\DataResources\Api\Services;


use Core\Categories\Models\Category;
use Core\Categories\Models\CategoryDateTime;
use Core\Categories\Services\CategoryDateTimesService;
use Core\Info\Models\City;
use Core\Settings\Services\SettingsService;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleOrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
        $categoryId = null;
        $type       = null;
        $cities     = [];
        if ($this->type == 'home_maid_sale') {
            $type       = 'maid';
            $category   = Category::where('slug','maid-host')->first();
            $categoryId = $category->id;
            $cities     = $category->cities->pluck('id')->toArray();
        } elseif ($this->type == 'care_host_sale') {
            $type       = 'host';
            $categories = Category::whereIn('slug', ['host-service', 'care-service', 'selfcare-service'])->get();
            $categoryId = $categories->pluck('id')->toArray();
            $cities     = City::whereHas('categories',function($categoriesQuery)use($categoryId){
                $categoriesQuery->whereIn('id',$categoryId);
            })->pluck('id')->toArray();
        }
        
        $isAvailable = (($this->for_all_cities || in_array(auth('api')->user()?->profile?->city_id,$cities)) and $this->status == "active");
        $message        = (!$isAvailable) ? SettingsService::getDataBaseSetting('not_available_message_'.config('app.locale')) : null;
        $serviceDates   = CategoryDateTimesService::getDateTimes(type: $type,categoryIds: $categoryId);
        $serviceDates = CategoryDateTimesService::getDateTimesFormatted('delivery', $serviceDates);
        

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'hours_num'     => $this->hours_num,
            'workers_num'   => $this->workers_num,
            'dates'         => $serviceDates,
            'availability'  => ['is_available' => $isAvailable, 'message' => $message],
        ];
    }
}

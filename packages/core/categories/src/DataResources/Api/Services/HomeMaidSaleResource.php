<?php

namespace Core\Categories\DataResources\Api\Services;

use App\Models\CustomService;
use Core\Categories\Models\Category;
use Core\Settings\Helpers\ToolHelper;
use Core\Users\Models\Fav;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeMaidSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->type=='home_maid_sale'){
            $cities = Category::whereSlug('maid-host')
            ->active()->first()
            ->cities->pluck('id');
        }elseif($this->type=='care_host_sale'){
            $cities = Category::whereIn('slug',['host-service','care-service','selfcare-service'])
            ->active()->first()
            ->cities->pluck('id');
        }
        $isAvailable = (($this->for_all_cities || in_array(auth('api')->user()?->profile?->city_id,$cities)) and $this->status == "active");
        $message        = '';
        if($isAvailable==false){
            $message = __('app.front.not_available');
        }


        return [
            'id'            => $this->id ,
            'name'          => $this->name ,
            'desc'          => $this->desc_mobile ,
            'price'         => ToolHelper::getPriceBasedOnCurrentWeekDay($this->price),
            'sale_price'    => ToolHelper::getPriceBasedOnCurrentWeekDay($this->sale_price),
            'hours_num'     => $this->hours_num,
            'workers_num'   => $this->workers_num,
            'image'         => $this->home_maid_sale_image ,
            'availability'  => ['is_available'=> $isAvailable,'message'=>$message],
            'is_fav'        => Fav::where('favs_type',Category::class)->where('favs_id',$this->id)->where('user_id',auth('api')->user()?->id)->exists(),


        ];
    }
}

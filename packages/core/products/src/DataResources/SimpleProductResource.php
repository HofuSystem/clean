<?php

namespace Core\Products\DataResources;

use Core\Categories\Models\CategoryTranslation;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Products\Models\ProductTranslation;
use Core\Products\Services\ProductsService;
use Core\Settings\Helpers\ToolHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = auth('api')->user();
        ProductsService::setCurrentContract($user);
        $data = ProductsService::getProductData($user,$this->resource);
        return [
            'id'                => $this->id ,
            'name'              => $this->name ,
            'name_ar'           => $this->translate('ar')->name,
            'name_en'           => $this->translate('en')->name,
            'image'             => MediaCenterHelper::getImagesUrl($this->image)  ,
            'price'             => $data['price'],
            'cost'              => $data['cost'],
            'desc'              => (string)$this->desc  ,
            'desc_ar'           => $this->translate('ar')->desc,
            'desc_en'           => $this->translate('en')->desc,
            'delivery_price'    => (double)$this->delivery_price,
            'available_quantity'=> (int)$this->quantity  ,
            'is_fav'            => $this->favers()->where('user_id',auth('api')->id())->first() ? true : false ,
            'category'          => $this->category?->name,
            'sub_category'      => $this->subCategory?->name,
            'sub_category_ar'   => $this->subCategory?->translate('ar')->name,
            'sub_category_en'   => $this->subCategory?->translate('en')->name,
        ];
    }
}

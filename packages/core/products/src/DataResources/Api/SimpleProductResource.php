<?php

namespace Core\Products\DataResources\Api;

use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Products\Models\Product;
use Core\Products\Services\ProductsService;
use Core\Settings\Helpers\ToolHelper;
use Core\Users\Models\Fav;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Products\DataResources\Api\ProductServiceSettingResource;

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
        $company = $user?->company ?? null;
        $cityId = $user?->profile?->city_id ?? null;
        ProductsService::setCurrentContract($company);
        $data = ProductsService::getProductData($company,'client',$cityId,$this->resource);

        $productSettings = $this->relationLoaded('productSettings')
            ? $this->productSettings
            : $this->productSettings()
                ->whereNull('parent_id')
                ->active()
                ->with([
                    'translations',
                    'productSettings' => function ($q) {
                        $q->active()
                            ->whereHas('products', function ($pq) {
                                $pq->where('products.id', $this->id);
                            })
                            ->with('translations');
                    }
                ])
                ->get();

        $userId = auth('api')->id();
        $isFav = false;
        if ($userId) {
            $isFav = $this->relationLoaded('favers')
                ? $this->favers->contains('user_id', $userId)
                : $this->favers()->where('user_id', $userId)->exists();
        }

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'name_ar'           => $this->translate('ar')->name,
            'name_en'           => $this->translate('en')->name,
            'image'             => MediaCenterHelper::getImagesUrl($this->image) ,
            'price'             => $data['price'],
            'points'            => (double)$this->points,
            'cost'              => $data['cost'],
            'desc'              => (string)$this->desc,
            'desc_ar'           => $this->translate('ar')->desc,
            'desc_en'           => $this->translate('en')->desc,
            // 'delivery_price'    => (double)$this->delivery_price,
            'available_quantity'=> (int)$this->quantity ,
            'is_fav'            => $isFav,
            'category'          => $this->category?->name,
            'sub_category'      => $this->subCategory?->name,
            'sub_category_ar'   => $this->subCategory?->translate('ar')->name,
            'sub_category_en'   => $this->subCategory?->translate('en')->name,
            'customizations'    => ProductServiceSettingResource::collection($productSettings),
        ];
    }
}

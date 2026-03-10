<?php

namespace Core\Products\DataResources;

use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Products\Services\ProductsService;
use Core\Settings\Helpers\ToolHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCardResource extends JsonResource
{
  
    public static $cityId = null;
    public static $user = null;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        ProductsService::setCurrentContract(self::$user);

        $data = ProductsService::getProductData(self::$user,$this->resource);
        $data = [
            'id'                => $this->id,
            'sku'               => $this->sku,
            'image'             => MediaCenterHelper::getImagesUrl($this->image),
            'name'              => $this->name,
            'price'             => $data['price'],
            'cost'              => $data['cost'],
            'type'              => $this->type,
            'category'          => $this->category?->name,
            'category_id'       => $this->category?->id,
            'sub_category'      => $this->subCategory?->name,
            'sub_category_id'   => $this->subCategory?->id,
            'in_contract'       => 0,
        ];
        if(ProductsService::getCurrentContract()){
            $contractPrice = $this->contractsPrices->where('product_id',$this->id)->where('contract_id',ProductsService::getCurrentContract()->id)->first();
            $contractCustomerPrice = $this->contractCustomerPrices->where('product_id',$this->id)->where('contract_id',ProductsService::getCurrentContract()->id)->first();
            if($contractPrice || $contractCustomerPrice){
                $data['in_contract'] = 1;
            }
        }
        if(!isset(self::$cityId)){
            $data['prices'] = $this->prices;
        }
        return $data;
    }
}

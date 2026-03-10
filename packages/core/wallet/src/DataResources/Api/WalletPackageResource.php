<?php

namespace Core\Wallet\DataResources\Api;

use Core\MediaCenter\Helpers\MediaCenterHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'image'     => MediaCenterHelper::getImageUrl($this->image) ,
            'price'     => (float) $this->price,
            'value'     => (float) $this->value,
            'add_date'  =>$this->created_at?->format('d-m-Y H:i'),
        ];
    }
}

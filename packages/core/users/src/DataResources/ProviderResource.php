<?php

namespace Core\Users\DataResources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                        => $this->id,
            'user_type'                 => $this->roles->first()->name,
            'fullname'                  => $this->fullname,
            'phone'                     => (string)$this->phone,
            "image"                     => $this->avatar_url,
            'wallet'                    => (double)$this->wallet,
            'is_allow_notify'           =>(boolean)$this->is_allow_notify,
            'unread_notifications'      => $this->unreadnotifications->count(),
            'token'                     => $this->when($this->token,$this->token),
        ];
    }
}

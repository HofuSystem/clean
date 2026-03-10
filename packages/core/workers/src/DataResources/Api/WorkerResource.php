<?php

namespace Core\Workers\DataResources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkerResource extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->name,
            'phone'             => $this->phone,
            'email'             => $this->email,
            'years_experience'  => $this->years_experience,
            'address'           => $this->address,
            'birth_date'        => $this->birth_date,
            'hour_price'        => $this->hour_price,
            'gender'            => $this->gender,
            'identity'          =>$this->identity,
            'nationality'       => $this->nationality->name,
            'city'              =>$this->city->name,
            'image'             => $this->worker_image,
        ];
    }
}

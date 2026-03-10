<?php

namespace Core\Users\DataResources;

use Core\Admin\Helpers\DashboardDataTableFormatter;
use Core\Info\DataResources\CityResource;
use Core\Info\DataResources\DistrictResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
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
            'id'                        => $this->id,
            'user_type'                 => $this->roles->first()?->name,
            'fullname'                  => $this->fullname,
            'phone'                     => (string)$this->phone,
            'email'                     => $this->email,
            'date_of_birth'             => $this->date_of_birth,
            'gender'                    => $this->gender,
            "image"                     => $this->avatar_url,
            'wallet'                    => (double)$this->wallet,
            'points_balance'            => (int)$this->points_balance,
            'referral_code'             => (string)$this->referral_code,
            'referrals_count'           => (int)$this->myReferrals->count(),
            'referrals'                 => SimpleUserRecourse::collection($this->myReferrals),
            'is_allow_notify'           => (boolean)$this->is_allow_notify,
            'unread_notifications'      => $this->unreadnotifications->count(),
            'token'                     => $this->when($this->token,$this->token),
            'city'                      => new CityResource($this->profile?->city) ,
            'district'                  => new DistrictResource($this->profile?->district) ,
            'other_city_name'           => $this->other_city_name,
            'lat'                       => (double)$this->profile?->lat,
            'lng'                       => (double)$this->profile?->lng,
        ];
    }
}

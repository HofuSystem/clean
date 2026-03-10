<?php

namespace Core\Users\Observers;

use Core\Users\Models\Address;

class AddressObserver
{
    /**
     * Handle the Address "creating" event.
     *
     * @param  \Core\Users\Models\Address  $address
     * @return void
     */
    public function creating(Address $address)
    {
    
    }
    /**
     * Handle the Address "created" event.
     *
     * @param  \Core\Users\Models\Address  $address
     * @return void
     */
    public function created(Address $address)
    {
       
    }

    /**
     * Handle the Address "updating" event.
     *
     * @param  \Core\Users\Models\Address  $address
     * @return void
     */
    public function updating(Address $address)
    {

    }
    /**
     * Handle the Address "updated" event.
     *
     * @param  \Core\Users\Models\Address  $address
     * @return void
     */
    public function updated(Address $address)
    {
        $address->updateQuietly(['description' => null]);
    }
    /**
     * Handle the Address "saving" event.
     *
     * @param  \Core\Users\Models\Address  $address
     * @return void
     */
    public function saving(Address $address)
    {

    }
    /**
     * Handle the Address "saved" event.
     *
     * @param  \Core\Users\Models\Address  $address
     * @return void
     */
    public function saved(Address $address)
    {
        if($address->is_default){
            Address::where('user_id',$address->user_id)
                ->whereNot('id',$address->id)
                ->update(['is_default' => false]);
        }
        if($address->name =='home'){
            if($address?->user?->profile){
                $address?->user?->profile->update(['city_id' => $address->city_id, 'district_id' => $address->district_id]);
            }
        }
    }

    /**
     * Handle the Address "deleted" event.
     *
     * @param  \Core\Users\Models\Address  $address
     * @return void
     */
    public function deleted(Address $address)
    {
      
    }

    /**
     * Handle the Address "restored" event.
     *
     * @param  \Core\Users\Models\Address  $address
     * @return void
     */
    public function restored(Address $address)
    {
        //
    }

    /**
     * Handle the Address "force deleted" event.
     *
     * @param  \Core\Users\Models\Address  $address
     * @return void
     */
    public function forceDeleted(Address $address)
    {
        //
    }
}

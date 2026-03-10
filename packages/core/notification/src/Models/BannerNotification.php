<?php

namespace Core\Notification\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Notification\Observers\BannerNotificationObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\Users\Models\User;

#[ObservedBy([BannerNotificationObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class BannerNotification extends CoreModel {
    
	protected $table             = 'banner_notifications';
	protected $fillable          = ['image', 'publish_date', 'expired_date', 'next_vision_hour', 'status', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter date on  publish_date
        if((request()->has("filters.from_publish_date")) and !empty(request("filters.from_publish_date"))){
            $query->where("publish_date",">=",Carbon::parse(request("filters.from_publish_date")));
        }

        if((request()->has("filters.to_publish_date")) and !empty(request("filters.to_publish_date"))){
            $query->where("publish_date","<=",Carbon::parse(request("filters.to_publish_date")));
        }
        
        //filter date on  expired_date
        if((request()->has("filters.from_expired_date")) and !empty(request("filters.from_expired_date"))){
            $query->where("expired_date",">=",Carbon::parse(request("filters.from_expired_date")));
        }

        if((request()->has("filters.to_expired_date")) and !empty(request("filters.to_expired_date"))){
            $query->where("expired_date","<=",Carbon::parse(request("filters.to_expired_date")));
        }
        
        //filter by number on  next_vision_hour
        if((request()->has("filters.next_vision_hour")) and !empty(request("filters.next_vision_hour"))){
            $query->where("next_vision_hour",request("filters.next_vision_hour"));
        }
        
        //filter select on  status
        if((request()->has("filters.status")) and !empty(request("filters.status"))){
            $query->where("status",request("filters.status"));
        }
        
        //filter date on  created_at
        if((request()->has("filters.from_created_at")) and !empty(request("filters.from_created_at"))){
            $query->whereDate("created_at",">=",Carbon::parse(request("filters.from_created_at")));
        }

        if((request()->has("filters.to_created_at")) and !empty(request("filters.to_created_at"))){
            $query->whereDate("created_at","<=",Carbon::parse(request("filters.to_created_at")));
        }
        
        //filter date on  updated_at
        if((request()->has("filters.from_updated_at")) and !empty(request("filters.from_updated_at"))){
            $query->whereDate("updated_at",">=",Carbon::parse(request("filters.from_updated_at")));
        }

        if((request()->has("filters.to_updated_at")) and !empty(request("filters.to_updated_at"))){
            $query->whereDate("updated_at","<=",Carbon::parse(request("filters.to_updated_at")));
        }
        if(request()->has('trash') and request()->trash == 1){
            $query->onlyTrashed();
        }
    }
  
    //end Scopes

    //start relations
    public function users()
    {
        return $this->morphToMany(User::class, 'notifications', 'users_notifications');
    }
    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('banner-notifications');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('banner-notifications');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('banner-notifications');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('banner-notifications');
    }
    //end Attributes

}

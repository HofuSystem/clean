<?php

namespace Core\Notification\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Models\User;
use Core\Notification\Observers\NotificationObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\Orders\Models\Order;

#[ObservedBy([NotificationObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Notification extends CoreModel {

	protected $table             = 'notifications';
	protected $fillable          = ['types', 'for', 'for_data','payload', 'title', 'body', 'media', 'sender_id','order_id','register_from','register_to','orders_from','orders_to','orders_min','orders_max', 'creator_id', 'updater_id'];
    protected $guarded           = [];


    //start Scopes
    function scopeSearch($query){

        //filter select on  types
        if((request()->has("filters.types")) and !empty(request("filters.types"))){
            $query->where("types",request("filters.types"));
        }

        //filter select on  for
        if((request()->has("filters.for")) and !empty(request("filters.for"))){
            $query->where("for",request("filters.for"));
        }

        //filter text on  title
        if((request()->has("filters.title")) and !empty(request("filters.title"))){
            $query->where("title","LIKE","%".request("filters.title")."%");
        }

        //filter text on  body
        if((request()->has("filters.body")) and !empty(request("filters.body"))){
            $query->where("body","LIKE","%".request("filters.body")."%");
        }

        //filter select on  sender
        if((request()->has("filters.sender_id")) and !empty(request("filters.sender_id"))){
            $query->whereRelation("sender","id",request("filters.sender_id"));
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

        if((request()->has("for_user")) and !empty(request("for_user"))){
            $query->whereHas('users',function($userQuery){
                $userQuery->where('users.id',request("for_user"));
            });
        }
        if((request()->has("filters.phone")) and !empty(request("filters.phone"))){
            $query->whereHas('users',function($userQuery){
                $userQuery->where('users.phone','LIKE','%'.request("filters.phone").'%');
            });
        }
        if((request()->has("filters.reference_id")) and !empty(request("filters.reference_id"))){
            $query->whereHas('order',function($orderQuery){
                $orderQuery->where('orders.reference_id','LIKE','%'.request("filters.reference_id").'%');
            });
        }
        if((request()->has("filters.city_id")) and !empty(request("filters.city_id"))){
            $query->whereHas('order.client.profile',function($profileQuery){
                $profileQuery->where('profiles.city_id',request("filters.city_id"));
            });
        }
        if(request()->has('trash') and request()->trash == 1){
            $query->onlyTrashed();
        }
        

    }

    //end Scopes

    //start relations

    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
    public function sender(){
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }
    public function users()
    {
        return $this->morphToMany(User::class, 'notifications', 'users_notifications');
    }



    //end relations

    //start Attributes

    public function getForDataArrayAttribute(){
        return json_decode($this->for_data,true) ?? [];
    }
    public function getActionsAttribute(){
      return $this->getActions('notifications');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('notifications');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('notifications');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('notifications');
    }
    //end Attributes

}

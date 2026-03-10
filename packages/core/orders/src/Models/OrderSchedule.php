<?php

namespace Core\Orders\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Models\User;
use Core\Workers\Models\Worker;
use Core\Orders\Observers\OrderScheduleObserver;

use Core\Settings\Models\CoreModel;
use App\Observers\GlobalModelObserver;
use Core\Settings\Traits\CoreModelTrait;
use Carbon\Carbon;
use Core\Users\Models\Address;

#[ObservedBy([OrderScheduleObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class OrderSchedule extends CoreModel {
	protected $table             = 'order_schedules';
	protected $fillable          = ['client_id', 'type', 'receiver_day', 'receiver_date', 'receiver_time', 'receiver_to_time', 'delivery_day', 'delivery_date', 'delivery_time', 'delivery_to_time', 'receiver_address_id', 'delivery_address_id', 'note', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  client
        if((request()->has("filters.client_id")) and !empty(request("filters.client_id"))){
            $query->whereRelation("client","id",request("filters.client_id"));
        }
        
        //filter select on  type
        if((request()->has("filters.type")) and !empty(request("filters.type"))){
            $query->where("type",request("filters.type"));
        }
        
        //filter select on  receiver_day
        if((request()->has("filters.receiver_day")) and !empty(request("filters.receiver_day"))){
            $query->where("receiver_day",request("filters.receiver_day"));
        }
        
        //filter date on  receiver_date
        if((request()->has("filters.from_receiver_date")) and !empty(request("filters.from_receiver_date"))){
            $query->where("receiver_date",">=",Carbon::parse(request("filters.from_receiver_date")));
        }

        if((request()->has("filters.to_receiver_date")) and !empty(request("filters.to_receiver_date"))){
            $query->where("receiver_date","<=",Carbon::parse(request("filters.to_receiver_date")));
        }
        
        //filter time on  receiver_time
        if((request()->has("filters.from_receiver_time")) and !empty(request("filters.from_receiver_time"))){
            $query->where("receiver_time",">=",Carbon::parse(request("filters.from_receiver_time")));
        }

        if((request()->has("filters.to_receiver_time")) and !empty(request("filters.to_receiver_time"))){
            $query->where("receiver_time","<=",Carbon::parse(request("filters.to_receiver_time")));
        }
        
        //filter time on  receiver_to_time
        if((request()->has("filters.from_receiver_to_time")) and !empty(request("filters.from_receiver_to_time"))){
            $query->where("receiver_to_time",">=",Carbon::parse(request("filters.from_receiver_to_time")));
        }

        if((request()->has("filters.to_receiver_to_time")) and !empty(request("filters.to_receiver_to_time"))){
            $query->where("receiver_to_time","<=",Carbon::parse(request("filters.to_receiver_to_time")));
        }
        
        //filter select on  delivery_day
        if((request()->has("filters.delivery_day")) and !empty(request("filters.delivery_day"))){
            $query->where("delivery_day",request("filters.delivery_day"));
        }
        
        //filter date on  delivery_date
        if((request()->has("filters.from_delivery_date")) and !empty(request("filters.from_delivery_date"))){
            $query->where("delivery_date",">=",Carbon::parse(request("filters.from_delivery_date")));
        }

        if((request()->has("filters.to_delivery_date")) and !empty(request("filters.to_delivery_date"))){
            $query->where("delivery_date","<=",Carbon::parse(request("filters.to_delivery_date")));
        }
        
        //filter time on  delivery_time
        if((request()->has("filters.from_delivery_time")) and !empty(request("filters.from_delivery_time"))){
            $query->where("delivery_time",">=",Carbon::parse(request("filters.from_delivery_time")));
        }

        if((request()->has("filters.to_delivery_time")) and !empty(request("filters.to_delivery_time"))){
            $query->where("delivery_time","<=",Carbon::parse(request("filters.to_delivery_time")));
        }
        
        //filter time on  delivery_to_time
        if((request()->has("filters.from_delivery_to_time")) and !empty(request("filters.from_delivery_to_time"))){
            $query->where("delivery_to_time",">=",Carbon::parse(request("filters.from_delivery_to_time")));
        }

        if((request()->has("filters.to_delivery_to_time")) and !empty(request("filters.to_delivery_to_time"))){
            $query->where("delivery_to_time","<=",Carbon::parse(request("filters.to_delivery_to_time")));
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
    
    public function client(){
        return $this->belongsTo(User::class, 'client_id', 'id');
    }

    public function receiverAddress(){
        return $this->belongsTo(Address::class, 'receiver_address_id', 'id');
    }

    public function deliveryAddress(){
        return $this->belongsTo(Address::class, 'delivery_address_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('order-schedules');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('order-schedules');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('order-schedules');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('order-schedules');
    }
    //end Attributes

}

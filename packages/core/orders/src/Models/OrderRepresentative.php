<?php

namespace Core\Orders\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Models\User;
use Core\Orders\Observers\OrderRepresentativeObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\Users\Models\Address;

#[ObservedBy([OrderRepresentativeObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class OrderRepresentative extends CoreModel {
    
	protected $table             = 'order_representatives';
	protected $fillable          = ['order_id', 'representative_id', 'type', 'date', 'time', 'to_time', 'lat', 'lng', 'location', 'has_problem', 'for_all_items', 'address_id','creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  order
        if((request()->has("filters.order_id")) and !empty(request("filters.order_id"))){
            $query->whereRelation("order","id",request("filters.order_id"));
        }
        
        //filter select on  representative
        if((request()->has("filters.representative_id")) and !empty(request("filters.representative_id"))){
            $query->whereRelation("representative","id",request("filters.representative_id"));
        }
        
        //filter select on  type
        if((request()->has("filters.type")) and !empty(request("filters.type"))){
            $query->where("type",request("filters.type"));
        }
        
        //filter date on  date
        if((request()->has("filters.from_date")) and !empty(request("filters.from_date"))){
            $query->where("date",">=",Carbon::parse(request("filters.from_date")));
        }

        if((request()->has("filters.to_date")) and !empty(request("filters.to_date"))){
            $query->where("date","<=",Carbon::parse(request("filters.to_date")));
        }
        
        //filter time on  time
        if((request()->has("filters.from_time")) and !empty(request("filters.from_time"))){
            $query->where("time",">=",Carbon::parse(request("filters.from_time")));
        }

        if((request()->has("filters.to_time")) and !empty(request("filters.to_time"))){
            $query->where("time","<=",Carbon::parse(request("filters.to_time")));
        }
        
        //filter time on  to_time
        if((request()->has("filters.from_to_time")) and !empty(request("filters.from_to_time"))){
            $query->where("to_time",">=",Carbon::parse(request("filters.from_to_time")));
        }

        if((request()->has("filters.to_to_time")) and !empty(request("filters.to_to_time"))){
            $query->where("to_time","<=",Carbon::parse(request("filters.to_to_time")));
        }
        
        //filter text on  lat
        if((request()->has("filters.lat")) and !empty(request("filters.lat"))){
            $query->where("lat","LIKE","%".request("filters.lat")."%");
        }
        
        //filter text on  lng
        if((request()->has("filters.lng")) and !empty(request("filters.lng"))){
            $query->where("lng","LIKE","%".request("filters.lng")."%");
        }
        
        //filter text on  location
        if((request()->has("filters.location")) and !empty(request("filters.location"))){
            $query->where("location","LIKE","%".request("filters.location")."%");
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
    
    public function address(){
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }
    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function representative(){
        return $this->belongsTo(User::class, 'representative_id', 'id');
    }

    public function items(){
        //                                                           you many need to swap these last att
		return $this->belongsToMany(OrderItem::class, 'order_representatives_order_items', 'order_representative_id', 'order_item_id');
	}

    //end relations

    //start Attributes
    public function getTime12HoursFormatAttribute(){
        return $this->time ? Carbon::parse($this->time)->format('h:iA') : null;
    }
    public function getToTime12HoursFormatAttribute(){
        return $this->to_time ? Carbon::parse($this->to_time)->format('h:iA') : null;
    }
    public function getActionsAttribute(){
      return $this->getActions('order-representatives');
    }

    public function getItemsActionsAttribute(){
        $slug = 'order-representatives';
        $actions = '<div class ="d-flex justify-content-center">';
        if ($this->representative and auth('web')->check() and auth('web')->user()->can('dashboard.notifications.create')) {
            if(
                ($this->type == 'receiver' and !in_array($this->order?->status, ['canceled', 'issue']))
                or
                ($this->type == 'delivery' and !in_array($this->order?->status,['pending','receiving_driver_accepted']))
                or
                ($this->type == 'technical')
            ){
                $actions .= ' <button class="btn btn-warning notify-item mx-1" data-representative-id="' .$this->representative_id . '"><i class="fas fa-bell"></i></button>';

            }
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.edit')) {
            $actions .= ' <button class="btn-operation edit-item mx-1" data-href="' . route('dashboard.'.$slug.'.edit', ['id' => $this->id]) . '"><i class="fas fa-pencil-alt"></i></button>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.delete')) {
            $actions .= '<button class="btn-operation delete-item mx-1" data-href="' . route('dashboard.'.$slug.'.delete', ['id' => $this->id]) . '"> <i class="fas fa-trash"></i></button></td>';
        }
     

        $actions .= '</div>';
        return $actions;
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('order-representatives');
    }

    public function getItemDataAttribute(){
        $data           = $this->getItemData('order-representatives');
        $data['items']  = $this->items->pluck('id');
        return $data; 
    }
    //end Attributes

}

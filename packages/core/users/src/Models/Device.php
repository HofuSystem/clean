<?php

namespace Core\Users\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Observers\DeviceObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([DeviceObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Device extends CoreModel {
    
	protected $table             = 'devices';
	protected $fillable          = ['device_token', 'type', 'user_id', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  device_token
        if((request()->has("filters.device_token")) and !empty(request("filters.device_token"))){
            $query->where("device_token","LIKE","%".request("filters.device_token")."%");
        }
        
        //filter select on  type
        if((request()->has("filters.type")) and !empty(request("filters.type"))){
            $query->where("type",request("filters.type"));
        }
        
        //filter select on  user
        if((request()->has("filters.user_id")) and !empty(request("filters.user_id"))){
            $query->whereRelation("user","id",request("filters.user_id"));
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
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('devices');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('devices');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('devices');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('devices');
    }
    //end Attributes

}

<?php

namespace Core\Users\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Observers\PointObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([PointObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Point extends CoreModel {
    
	protected $table             = 'points';
	protected $fillable          = ['title', 'amount', 'operation', 'expire_at', 'user_id', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  title
        if((request()->has("filters.title")) and !empty(request("filters.title"))){
            $query->where("title","LIKE","%".request("filters.title")."%");
        }
        
        //filter text on  amount
        if((request()->has("filters.amount")) and !empty(request("filters.amount"))){
            $query->where("amount","LIKE","%".request("filters.amount")."%");
        }
        if((request()->has("filters.user_id")) and !empty(request("filters.user_id"))){
            $query->where("user_id",request("filters.user_id"));
        }
        
        //filter select on  operation
        if((request()->has("filters.operation")) and !empty(request("filters.operation"))){
            $query->where("operation",request("filters.operation"));
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
      return $this->getActions('points');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('points');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('points');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('points');
    }
    //end Attributes

}

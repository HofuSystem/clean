<?php

namespace Core\Users\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Observers\BanObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([BanObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Ban extends CoreModel {
    
	protected $table             = 'bans';
	protected $fillable          = ['level', 'value', 'admin_id', 'reason', 'from', 'to', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  level
        if((request()->has("filters.level")) and !empty(request("filters.level"))){
            $query->where("level",request("filters.level"));
        }
        
        //filter text on  value
        if((request()->has("filters.value")) and !empty(request("filters.value"))){
            $query->where("value","LIKE","%".request("filters.value")."%");
        }
        
        //filter select on  admin
        if((request()->has("filters.admin_id")) and !empty(request("filters.admin_id"))){
            $query->whereRelation("admin","id",request("filters.admin_id"));
        }
        
        //filter text on  reason
        if((request()->has("filters.reason")) and !empty(request("filters.reason"))){
            $query->where("reason","LIKE","%".request("filters.reason")."%");
        }
        
        //filter date on  from
        if((request()->has("filters.from_from")) and !empty(request("filters.from_from"))){
            $query->whereDate("from",">=",Carbon::parse(request("filters.from_from")));
        }

        if((request()->has("filters.to_from")) and !empty(request("filters.to_from"))){
            $query->whereDate("from","<=",Carbon::parse(request("filters.to_from")));
        }
        
        //filter date on  to
        if((request()->has("filters.from_to")) and !empty(request("filters.from_to"))){
            $query->whereDate("to",">=",Carbon::parse(request("filters.from_to")));
        }

        if((request()->has("filters.to_to")) and !empty(request("filters.to_to"))){
            $query->whereDate("to","<=",Carbon::parse(request("filters.to_to")));
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
    
    public function admin(){
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('bans');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('bans');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('bans');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('bans');
    }
    //end Attributes

}

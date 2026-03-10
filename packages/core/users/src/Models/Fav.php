<?php

namespace Core\Users\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Observers\FavObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([FavObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Fav extends CoreModel {
    
	protected $table             = 'favs';
	protected $fillable          = ['favs_type', 'favs_id', 'user_id', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  favs_type
        if((request()->has("filters.favs_type")) and !empty(request("filters.favs_type"))){
            $query->where("favs_type","LIKE","%".request("filters.favs_type")."%");
        }
        
        //filter text on  favs_id
        if((request()->has("filters.favs_id")) and !empty(request("filters.favs_id"))){
            $query->where("favs_id","LIKE","%".request("filters.favs_id")."%");
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
      return $this->getActions('favs');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('favs');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('favs');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('favs');
    }
    //end Attributes

}

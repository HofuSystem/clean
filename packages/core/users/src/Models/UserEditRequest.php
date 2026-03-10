<?php

namespace Core\Users\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Observers\UserEditRequestObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;

#[ObservedBy([UserEditRequestObserver::class])]
class UserEditRequest extends CoreModel {
    
	protected $table             = 'user_edit_requests';
	protected $fillable          = ['fullname', 'email', 'phone', 'user_id', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  fullname
        if((request()->has("filters.fullname")) and !empty(request("filters.fullname"))){
            $query->where("fullname","LIKE","%".request("filters.fullname")."%");
        }
        
        //filter by email on  email
        if((request()->has("filters.email")) and !empty(request("filters.email"))){
            $query->where("email","LIKE","%".request("filters.email")."%");
        }
        
        //filter text on  phone
        if((request()->has("filters.phone")) and !empty(request("filters.phone"))){
            $query->where("phone","LIKE","%".request("filters.phone")."%");
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
      return $this->getActions('user-edit-requests');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('user-edit-requests');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('user-edit-requests');
    }
    //end Attributes

}

<?php

namespace Core\Workers\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Info\Models\Nationality;
use Core\Info\Models\City;
use Core\Categories\Models\Category;
use Core\Users\Models\User;
use Core\Workers\Observers\WorkerObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([WorkerObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Worker extends CoreModel {
    
	protected $table             = 'workers';
	protected $fillable          = ['image', 'name', 'phone', 'email', 'years_experience', 'address', 'birth_date', 'hour_price', 'gender', 'status', 'identity', 'nationality_id', 'city_id', 'leader_id','creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  name
        if((request()->has("filters.name")) and !empty(request("filters.name"))){
            $query->where("name","LIKE","%".request("filters.name")."%");
        }
        
        //filter text on  phone
        if((request()->has("filters.phone")) and !empty(request("filters.phone"))){
            $query->where("phone","LIKE","%".request("filters.phone")."%");
        }
        
        //filter by email on  email
        if((request()->has("filters.email")) and !empty(request("filters.email"))){
            $query->where("email","LIKE","%".request("filters.email")."%");
        }
        
        //filter by number on  years_experience
        if((request()->has("filters.years_experience")) and !empty(request("filters.years_experience"))){
            $query->where("years_experience",request("filters.years_experience"));
        }
        
        //filter text on  address
        if((request()->has("filters.address")) and !empty(request("filters.address"))){
            $query->where("address","LIKE","%".request("filters.address")."%");
        }
        
        //filter date on  birth_date
        if((request()->has("filters.from_birth_date")) and !empty(request("filters.from_birth_date"))){
            $query->where("birth_date",">=",Carbon::parse(request("filters.from_birth_date")));
        }

        if((request()->has("filters.to_birth_date")) and !empty(request("filters.to_birth_date"))){
            $query->where("birth_date","<=",Carbon::parse(request("filters.to_birth_date")));
        }
        
        //filter by number on  hour_price
        if((request()->has("filters.hour_price")) and !empty(request("filters.hour_price"))){
            $query->where("hour_price",request("filters.hour_price"));
        }
        
        //filter select on  gender
        if((request()->has("filters.gender")) and !empty(request("filters.gender"))){
            $query->where("gender",request("filters.gender"));
        }
        
        //filter select on  status
        if((request()->has("filters.status")) and !empty(request("filters.status"))){
            $query->where("status",request("filters.status"));
        }
        
        //filter select on  identity
        if((request()->has("filters.identity")) and !empty(request("filters.identity"))){
            $query->where("identity",request("filters.identity"));
        }
        
        //filter select on  nationality
        if((request()->has("filters.nationality_id")) and !empty(request("filters.nationality_id"))){
            $query->whereRelation("nationality","id",request("filters.nationality_id"));
        }
        
        //filter select on  city
        if((request()->has("filters.city_id")) and !empty(request("filters.city_id"))){
            $query->whereRelation("city","id",request("filters.city_id"));
        }
        
        //filter select on  categories
        if((request()->has("filters.categories")) and !empty(request("filters.categories"))){
            $query->whereRelation("categories","id",request("filters.categories"));
        }
        
        //filter select on  leaders
        if((request()->has("filters.leader_id")) and !empty(request("filters.leader_id"))){
            $query->whereRelation("leader","id",request("filters.leader_id"));
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
    
    public function nationality(){
        return $this->belongsTo(Nationality::class, 'nationality_id', 'id');
    }

    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function categories(){
        //                                                           you many need to swap these last att
		return $this->belongsToMany(Category::class, 'worker_categories', 'worker_id', 'category_id');
	}

    public function leader(){
        //                                                           you many need to swap these last att
		return $this->belongsTo(User::class, 'leader_id',);
	}

    public function workdays(){
        return $this->hasMany(WorkerDay::class, 'worker_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('workers');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('workers');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('workers');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('workers');
    }
    //end Attributes

}

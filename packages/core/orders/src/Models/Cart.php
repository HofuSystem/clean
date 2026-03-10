<?php

namespace Core\Orders\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Models\User;
use Core\Orders\Observers\CartObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([CartObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Cart extends CoreModel {
    
	protected $table             = 'carts';
	protected $fillable          = ['user_id', 'phone', 'data', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  user
        if((request()->has("filters.user_id")) and !empty(request("filters.user_id"))){
            $query->whereRelation("user","id",request("filters.user_id"));
        }
        if((request()->has("filters.city_id")) and !empty(request("filters.city_id"))){
            $query->whereHas("user.profile", function($q){
                $q->where("city_id",request("filters.city_id"));
            });
        }
        
        //filter text on  phone
        if((request()->has("filters.phone")) and !empty(request("filters.phone"))){
            $query->where("phone","LIKE","%".request("filters.phone")."%");
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
      return $this->getActions('carts');
    }
    public function getActions($slug)
    {

        $actions = '<div class ="d-flex justify-content-center">';
        if(request()->has('trash') and request()->trash == 1){
            if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.restore')) {
                $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 btn-restore" href="' . route('dashboard.'.$slug.'.restore', ['id' => $this->id]) . '">
                    <i class="fa fa-trash-restore"></i> <span>' . trans('restore') . '</span>
                    </a>';
            }
            $actions .= '</div>';
            return $actions;
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.notifications.create')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 notify-btn" data-user-id="'.$this->user_id.'" href="' . route('dashboard.notifications.create', ['id' => $this->id]) . '"> 
                <i class="fas fa-bell"></i><span> ' . trans('notify') . ' </span>
                </a>';
        }
      
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.create-order')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1" href="' . route('dashboard.'.$slug.'.create-order', ['id' => $this->id]) . '"> 
                <i class="fas fa-cart-plus"></i><span> ' . trans('create order') . ' </span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.delete')) {
            $actions .= '<a class="btn-operation  d-flex justify-content-center align-items-center mx-1 delete-btn" href="' . route('dashboard.'.$slug.'.delete', ['id' => $this->id]) . '"> 
                <i class="fa fa-trash"></i><span> ' . trans('delete') . ' </span>
                </a>';
        }

        $actions .= '</div>';
        return $actions;
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('carts');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('carts');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('carts');
    }
    public function getSelectSwitchAttribute()
    {
        return
            '
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" data-user-id="'.$this->user_id.'" name="table_selected" value="' . $this->id . '" />
                </div>
            ';
    }
    //end Attributes

}

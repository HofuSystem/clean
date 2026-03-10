<?php

namespace Core\Users\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Info\Models\City;
use Core\Info\Models\District;
use Core\Users\Observers\AddressObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([AddressObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Address extends CoreModel {

	protected $table             = 'addresses';
	protected $fillable          = ['location','name', 'lat', 'lng','description', 'city_id', 'district_id', 'user_id','is_default', 'creator_id', 'updater_id', 'status'];
    protected $guarded           = [];


    //start Scopes
    function scopeSearch($query){

        //filter text on  name
        if((request()->has("filters.name")) and !empty(request("filters.name"))){
            $query->where("name","LIKE","%".request("filters.name")."%");
        }
        
        //filter text on  location
        if((request()->has("filters.location")) and !empty(request("filters.location"))){
            $query->where("location","LIKE","%".request("filters.location")."%");
        }

        //filter text on  lat
        if((request()->has("filters.lat")) and !empty(request("filters.lat"))){
            $query->where("lat","LIKE","%".request("filters.lat")."%");
        }

        //filter text on  lng
        if((request()->has("filters.lng")) and !empty(request("filters.lng"))){
            $query->where("lng","LIKE","%".request("filters.lng")."%");
        }

        //filter select on  city
        if((request()->has("filters.city_id")) and !empty(request("filters.city_id"))){
            $query->whereRelation("city","id",request("filters.city_id"));
        }

        //filter select on  district
        if((request()->has("filters.district_id")) and !empty(request("filters.district_id"))){
            $query->whereRelation("district","id",request("filters.district_id"));
        }

        //filter text on  is_default
        if((request()->has("filters.is_default")) and !empty(request("filters.is_default"))){
            $query->where("is_default","LIKE","%".request("filters.is_default")."%");
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
        if((request()->has("filters.user_id")) and !empty(request("filters.user_id"))){
            $query->where("user_id",request("filters.user_id"));
        }
        if(request()->has("forCompany") and request("forCompany") == true){
            $query->whereHas("user",function($userQuery){
                $userQuery->whereHas("roles",function($roleQuery){
                    $roleQuery->where("name","company");
                });
            });
        }
        if(request()->has('trash') and request()->trash == 1){
            $query->onlyTrashed();
        }
    }

    //end Scopes

    //start relations

    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function district(){
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    //end relations

    //start Attributes

    public function getActionsAttribute(){
        $slug = 'addresses';
        $forCompany = request()->has("forCompany") and request("forCompany") == true;
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
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.show',['forCompany' => $forCompany ?? false ])) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.'.$slug.'.show', ['id' => $this->id,'forCompany' => $forCompany ?? false ]) . '"> 
                <i class="fa fa-eye"></i> <span>' . trans('show') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.edit',['forCompany' => $forCompany ?? false ])) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.'.$slug.'.edit', ['id' => $this->id,'forCompany' => $forCompany ?? false ]) . '"> 
                <i class="fa fa-edit"></i> <span>' . trans('edit') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.delete',['forCompany' => $forCompany ?? false ])) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 delete-btn" href="' . route('dashboard.'.$slug.'.delete', ['id' => $this->id,'forCompany' => $forCompany ?? false ]) . '"> 
                <i class="fa fa-trash"></i><span> ' . trans('delete') . ' </span>
                </a>';
        }

        $actions .= '</div>';
        return $actions;
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('addresses');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('addresses');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('addresses');
    }
    //end Attributes

}

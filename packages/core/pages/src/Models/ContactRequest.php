<?php

namespace Core\Pages\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Pages\Observers\ContactRequestObserver;

use App\Observers\GlobalModelObserver;
use Carbon\Carbon;
use Core\Settings\Models\CoreModel;

#[ObservedBy([ContactRequestObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class ContactRequest extends CoreModel {
	protected $table             = 'contact_requests';
	protected $fillable          = ['name', 'phone', 'email', 'type', 'notes', 'creator_id', 'updater_id'];
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

        //filter select on  service
        if((request()->has("filters.service_id")) and !empty(request("filters.service_id"))){
            $query->whereRelation("service","id",request("filters.service_id"));
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

        //filter text on  notes
        if((request()->has("filters.notes")) and !empty(request("filters.notes"))){
            $query->where("notes","LIKE","%".request("filters.notes")."%");
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


    //end relations

    //start Attributes

    public function getActionsAttribute(){
      return $this->getActions('contact-requests');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('contact-requests');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('contact-requests');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('contact-requests');
    }
    //end Attributes

}

<?php

namespace Core\Orders\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Models\User;
use Core\Orders\Observers\OrderReportObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([OrderReportObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class OrderReport extends CoreModel {
    
	protected $table             = 'order_reports';
	protected $fillable          = ['order_id', 'user_id', 'report_reason_id', 'desc_location', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  order
        if((request()->has("filters.order_id")) and !empty(request("filters.order_id"))){
            $query->whereRelation("order","id",request("filters.order_id"));
        }
        
        //filter select on  user
        if((request()->has("filters.user_id")) and !empty(request("filters.user_id"))){
            $query->whereRelation("user","id",request("filters.user_id"));
        }
        
        //filter select on  reportReason
        if((request()->has("filters.report_reason_id")) and !empty(request("filters.report_reason_id"))){
            $query->whereRelation("reportReason","id",request("filters.report_reason_id"));
        }
        
        //filter text on  desc_location
        if((request()->has("filters.desc_location")) and !empty(request("filters.desc_location"))){
            $query->where("desc_location","LIKE","%".request("filters.desc_location")."%");
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
    
    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reportReason(){
        return $this->belongsTo(ReportReason::class, 'report_reason_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('order-reports');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('order-reports');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('order-reports');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('order-reports');
    }
    //end Attributes

}

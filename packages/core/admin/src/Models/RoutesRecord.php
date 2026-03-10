<?php

namespace Core\Admin\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Models\User;
use Core\Admin\Observers\RoutesRecordObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([RoutesRecordObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class RoutesRecord extends CoreModel {
    
	protected $table             = 'routes_records';
	protected $fillable          = ['version','headers','method','end_point', 'attributes', 'user_id', 'ip_address', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    
    function scopeInTimePeriod($query,$timeDuration){
        // Determine the time range
       $startTime  = null;
       $endTime    = Carbon::now();

       switch ($timeDuration) {
       case 'last-minute':
           $startTime = Carbon::now()->subMinute();
           break;
       case '10-minute':
           $startTime = Carbon::now()->subMinutes(10);
           break;
       case '30-minute':
           $startTime = Carbon::now()->subMinutes(30);
           break;
       case 'last-hour':
           $startTime = Carbon::now()->subHour();
           break;
       case 'last-day':
           $startTime = Carbon::now()->subDay();
           break;
       case 'last-week':
           $startTime = Carbon::now()->subWeek();
           break;
       case 'last-month':
           $startTime = Carbon::now()->subMonth();
           break;
       case 'last-year':
           $startTime = Carbon::now()->subYear();
           break;
       default: // 'all-time'
           $startTime = null;
       }
       // Apply time filter if not 'all-time'
       if ($startTime) {
           $query->whereBetween('routes_records.created_at', [$startTime, $endTime]);
       }
   }
    //start Scopes
    function scopeSearch($query){
        
        //filter text on  end_point
        if((request()->has("filters.end_point")) and !empty(request("filters.end_point"))){
            $query->where("end_point","LIKE","%".request("filters.end_point")."%");
        }
        
        //filter text on  attributes
        if((request()->has("filters.attributes")) and !empty(request("filters.attributes"))){
            $query->where("attributes","LIKE","%".request("filters.attributes")."%");
        }
        
        //filter select on  user
        if((request()->has("filters.user_id")) and !empty(request("filters.user_id"))){
            $query->whereRelation("user","id",request("filters.user_id"));
        }
        
        //filter select on  ipAddress
        if((request()->has("filters.ip_address")) and !empty(request("filters.ip_address"))){
            $query->whereRelation("ipAddress","id",request("filters.ip_address"));
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

    public function ipAddress(){
        return $this->belongsTo(User::class, 'ip_address', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('routes-records');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('routes-records');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('routes-records');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('routes-records');
    }
    //end Attributes

}

<?php

namespace Core\Workers\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Workers\Observers\WorkerDayObserver;

use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([WorkerDayObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class WorkerDay extends CoreModel {
    
	protected $table             = 'worker_days';
	protected $fillable          = ['worker_id', 'date', 'type', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    

    //start Scopes
    function scopeSearch($query){
        
        //filter select on  worker
        if((request()->has("filters.worker_id")) and !empty(request("filters.worker_id"))){
            $query->whereRelation("worker","id",request("filters.worker_id"));
        }
        
        //filter date on  date
        if((request()->has("filters.from_date")) and !empty(request("filters.from_date"))){
            $query->where("date",">=",Carbon::parse(request("filters.from_date")));
        }

        if((request()->has("filters.to_date")) and !empty(request("filters.to_date"))){
            $query->where("date","<=",Carbon::parse(request("filters.to_date")));
        }
        
        //filter select on  type
        if((request()->has("filters.type")) and !empty(request("filters.type"))){
            $query->where("type",request("filters.type"));
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
    
    public function worker(){
        return $this->belongsTo(Worker::class, 'worker_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('worker-days');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('worker-days');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('worker-days');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('worker-days');
    }
    //end Attributes

}

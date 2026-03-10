<?php

namespace Core\Orders\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Orders\Observers\ReportReasonObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([ReportReasonObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class ReportReason extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'report_reasons';
	protected $fillable          = ['ordering', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["name","desc"];

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  name
        if((request()->has("filters.name")) and !empty(request("filters.name"))){
            $query->whereTranslationLike("name","%".request("filters.name")."%");
        }
        
        //filter text on  desc
        if((request()->has("filters.desc")) and !empty(request("filters.desc"))){
            $query->where("desc"."_".config("app.locale"),"LIKE","%".request("filters.desc")."%");
        }
        
        //filter by number on  ordering
        if((request()->has("filters.ordering")) and !empty(request("filters.ordering"))){
            $query->where("ordering",request("filters.ordering"));
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
      return $this->getActions('report-reasons');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('report-reasons');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('report-reasons');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('report-reasons');
    }
    //end Attributes

}

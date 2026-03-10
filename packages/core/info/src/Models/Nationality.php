<?php

namespace Core\Info\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Info\Observers\NationalityObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([NationalityObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Nationality extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'nationalities';
	protected $fillable          = ['arranging', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["name"];

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  name
        if((request()->has("filters.name")) and !empty(request("filters.name"))){
            $query->whereTranslationLike("name","%".request("filters.name")."%");
        }
        
        //filter by number on  arranging
        if((request()->has("filters.arranging")) and !empty(request("filters.arranging"))){
            $query->where("arranging",request("filters.arranging"));
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
      return $this->getActions('nationalities');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('nationalities');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('nationalities');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('nationalities');
    }
    //end Attributes

}

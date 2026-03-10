<?php

namespace Core\Users\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Observers\PermissionObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([PermissionObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Permission extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'permissions';
	protected $fillable          = ['name', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["title"];

    //start Scopes
    function scopeSearch($query){
        //filter text on  title
        if((request()->has("filters.title")) and !empty(request("filters.title"))){
            $query->whereTranslationLike("title","%".request("filters.title")."%");
        }
        
        //filter text on  name
        if((request()->has("filters.name")) and !empty(request("filters.name"))){
            $query->where("name","LIKE","%".request("filters.name")."%");
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
      return $this->getActions('permissions');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('permissions');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('permissions');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('permissions');
    }
    //end Attributes

}

<?php

namespace Core\Pages\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Pages\Observers\BusinessObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use App\Observers\GlobalModelObserver;
use Carbon\Carbon;
use Core\Settings\Models\CoreModel;

#[ObservedBy([BusinessObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Business extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'businesses';
	protected $fillable          = ['icon',  'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["title","description"];

    //start Scopes
    function scopeSearch($query){

        //filter text on  title
        if((request()->has("filters.title")) and !empty(request("filters.title"))){
            $query->whereTranslationLike("title","%".request("filters.title")."%");
        }
        //filter text on  description
        if((request()->has("filters.description")) and !empty(request("filters.description"))){
            $query->whereTranslationLike("description","%".request("filters.description")."%");
        }

        //filter by number on  count
        if((request()->has("filters.icon")) and !empty(request("filters.count"))){
            $query->where("icon",request("filters.count"));
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
      return $this->getActions('businesses');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('businesses');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('businesses');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('businesses');
    }
    //end Attributes

}

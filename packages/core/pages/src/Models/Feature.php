<?php

namespace Core\Pages\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Pages\Observers\FeatureObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use App\Observers\GlobalModelObserver;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use Core\MediaCenter\Helpers\MediaCenterHelper;

#[ObservedBy([FeatureObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Feature extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'features';
	protected $fillable          = ['icon', 'image', 'section', 'is_active', 'creator_id', 'updater_id'];
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
            $query->where("description"."_".config("app.locale"),"LIKE","%".request("filters.description")."%");
        }

        //filter select on  section
        if((request()->has("filters.section")) and !empty(request("filters.section"))){
            $query->where("section",request("filters.section"));
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
    public function getIconUrlAttribute(){
        return MediaCenterHelper::getImageUrl($this->icon);
    }

    public function getImageUrlAttribute(){
        return $this->image ? MediaCenterHelper::getImageUrl($this->image) : null;
    }
    public function getActionsAttribute(){
      return $this->getActions('features');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('features');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('features');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('features');
    }
    //end Attributes

}

<?php

namespace Core\Pages\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Pages\Observers\PageObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use App\Observers\GlobalModelObserver;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;


#[ObservedBy([PageObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Page extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'pages';
	protected $fillable          = ['slug', 'image', 'is_active', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["title","description","meta_title","meta_description"];

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

    public function sections(){
        return $this->hasMany(Section::class, 'page_id', 'id')->orderBy('order');
    }

    //end relations

    //start Attributes

    public function getActionsAttribute(){
      return $this->getActions('pages');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('pages');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('pages');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('pages');
    }
    //end Attributes

}

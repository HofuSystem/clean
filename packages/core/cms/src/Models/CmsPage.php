<?php

namespace Core\CMS\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\CMS\Observers\CmsPageObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([CmsPageObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class CmsPage extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'cms_pages';
	protected $fillable          = ['slug', 'is_parent', 'is_multi_upload', 'have_point', 'have_name', 'have_description', 'have_intro', 'have_image', 'have_tablet_image', 'have_mobile_image', 'have_icon', 'have_video', 'have_link', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["name"];

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  slug
        if((request()->has("filters.slug")) and !empty(request("filters.slug"))){
            $query->where("slug","LIKE","%".request("filters.slug")."%");
        }
        
        //filter text on  name
        if((request()->has("filters.name")) and !empty(request("filters.name"))){
            $query->whereTranslationLike("name","%".request("filters.name")."%");
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
    
    public function details(){
        return $this->hasMany(CmsPageDetail::class, 'cms_pages_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('cms-pages');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('cms-pages');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('cms-pages');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('cms-pages');
    }
    //end Attributes

}

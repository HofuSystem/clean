<?php

namespace Core\Pages\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Pages\Observers\SectionObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use App\Observers\GlobalModelObserver;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use Core\MediaCenter\Helpers\MediaCenterHelper;

#[ObservedBy([SectionObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Section extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'sections';
	protected $fillable          = ['images', 'video', 'template', 'page_id', 'order', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["title","small_title","description"];

    //start Scopes
    function scopeSearch($query){

        //filter text on  title
        if((request()->has("filters.title")) and !empty(request("filters.title"))){
            $query->whereTranslationLike("title","%".request("filters.title")."%");
        }

        //filter text on  small_title
        if((request()->has("filters.small_title")) and !empty(request("filters.small_title"))){
            $query->whereTranslationLike("small_title","%".request("filters.small_title")."%");
        }

        //filter text on  description
        if((request()->has("filters.description")) and !empty(request("filters.description"))){
            $query->whereTranslationLike("description","%".request("filters.description")."%");
        }

        //filter text on  video
        if((request()->has("filters.video")) and !empty(request("filters.video"))){
            $query->where("video","LIKE","%".request("filters.video")."%");
        }

        //filter select on  template
        if((request()->has("filters.template")) and !empty(request("filters.template"))){
            $query->where("template",request("filters.template"));
        }

        //filter select on  page
        if((request()->has("filters.page_id")) and !empty(request("filters.page_id"))){
            $query->whereRelation("page","id",request("filters.page_id"));
        }

        //filter by number on  order
        if((request()->has("filters.order")) and !empty(request("filters.order"))){
            $query->where("order",request("filters.order"));
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

    public function page(){
        return $this->belongsTo(Page::class, 'page_id', 'id');
    }

    //end relations

    //start Attributes
    public function getImagesUrlsAttribute(){
        return MediaCenterHelper::getImagesUrl($this->images);
    }
    public function getImageUrlAttribute(){
        return MediaCenterHelper::getImageUrl($this->images);
    }
    public function getActionsAttribute(){
      return $this->getActions('sections');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('sections');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('sections');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('sections');
    }
    //end Attributes

}

<?php

namespace Core\Blog\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Blog\Observers\BlogCategoryObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([BlogCategoryObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class BlogCategory extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'blog_categories';
	protected $fillable          = ['parent_id', 'status', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["title"];

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  title
        if((request()->has("filters.title")) and !empty(request("filters.title"))){
            $query->whereTranslationLike("title","%".request("filters.title")."%");
        }
        
        //filter select on  parent
        if((request()->has("filters.parent_id")) and !empty(request("filters.parent_id"))){
            $query->whereRelation("parent","id",request("filters.parent_id"));
        }
        
        //filter select on  status
        if((request()->has("filters.status")) and !empty(request("filters.status"))){
            $query->where("status",request("filters.status"));
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
    
    public function parent(){
        return $this->belongsTo(BlogCategory::class, 'parent_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('blog-categories');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('blog-categories');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('blog-categories');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('blog-categories');
    }
    //end Attributes

}

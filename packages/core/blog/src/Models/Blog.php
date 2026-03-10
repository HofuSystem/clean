<?php

namespace Core\Blog\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Blog\Observers\BlogObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\MediaCenter\Helpers\MediaCenterHelper;

#[ObservedBy([BlogObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Blog extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'blogs';
	protected $fillable          = ['slug','image', 'gallery', 'category_id', 'status', 'published_at', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["title","content","meta"];

    //start Scopes
    function scopeSearch($query){

        //filter text on  title
        if((request()->has("filters.title")) and !empty(request("filters.title"))){
            $query->whereTranslationLike("title","%".request("filters.title")."%");
        }

        //filter text on  content
        if((request()->has("filters.content")) and !empty(request("filters.content"))){
            $query->whereTranslationLike("content","%".request("filters.content")."%");
        }

        //filter select on  category
        if((request()->has("filters.category_id")) and !empty(request("filters.category_id"))){
            $query->whereRelation("category","id",request("filters.category_id"));
        }

        //filter select on  status
        if((request()->has("filters.status")) and !empty(request("filters.status"))){
            $query->where("status",request("filters.status"));
        }

        //filter date on  published_at
        if((request()->has("filters.from_published_at")) and !empty(request("filters.from_published_at"))){
            $query->whereDate("published_at",">=",Carbon::parse(request("filters.from_published_at")));
        }

        if((request()->has("filters.to_published_at")) and !empty(request("filters.to_published_at"))){
            $query->whereDate("published_at","<=",Carbon::parse(request("filters.to_published_at")));
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
    function scopePublished($query){
        return $query->where('status','publish')->orWhere('published_at','<=',Carbon::now());
    }

    //end Scopes

    //start relations

    public function category(){
        return $this->belongsTo(BlogCategory::class, 'category_id', 'id');
    }

    //end relations

    //start Attributes
    public function getImageUrlAttribute(){
        return MediaCenterHelper::getImagesUrl($this->image);
    }

   //start Attributes
   function getFormattedContentAttribute(){
    // The goal is to add an alt attribute to all <img> tags in the content, setting its value to the blog's title.
    // We'll use a regex to find <img> tags and add/replace the alt attribute.
    $content = $this->content;

    // Callback to add or replace alt attribute with the blog's title
    $content = preg_replace_callback(
        '/<img\b([^>]*)>/i',
        function ($matches) {
            $attrs = $matches[1];

            // Remove any existing alt attribute
            $attrs = preg_replace('/\balt\s*=\s*([\'"]).*?\1/i', '', $attrs);

            // Clean up extra spaces
            $attrs = trim($attrs);

            // Add the alt attribute with the title
            $alt = ' alt="' . htmlspecialchars($this->title ?? '', ENT_QUOTES, 'UTF-8') . '"';

            // Ensure there's a space before attributes if needed
            $attrs = $attrs ? ' ' . $attrs : '';

            return '<img' . $attrs . $alt . '>';
        },
        $content
    );

    return $content;
    }
    public function getActionsAttribute(){
      return $this->getActions('blogs');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('blogs');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('blogs');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('blogs');
    }
    //end Attributes

}

<?php

namespace Core\Pages\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Pages\Observers\TestimonialObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use App\Observers\GlobalModelObserver;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use Core\MediaCenter\Helpers\MediaCenterHelper;

#[ObservedBy([TestimonialObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Testimonial extends CoreModel implements TranslatableContract
{
    use Translatable;
    
    protected $table             = 'testimonials';
    protected $fillable          = ['avatar', 'is_active', 'rating', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ['name', 'title', 'body', 'location'];

    //start Scopes
    function scopeSearch($query)
    {
        //filter text on name
        if ((request()->has("filters.name")) and !empty(request("filters.name"))) {
            $query->whereTranslationLike("name", "%" . request("filters.name") . "%");
        }

        //filter text on title
        if ((request()->has("filters.title")) and !empty(request("filters.title"))) {
            $query->whereTranslationLike("title", "%" . request("filters.title") . "%");
        }

        //filter text on body
        if ((request()->has("filters.body")) and !empty(request("filters.body"))) {
            $query->whereTranslationLike("body", "%" . request("filters.body") . "%");
        }

        //filter text on location
        if ((request()->has("filters.location")) and !empty(request("filters.location"))) {
            $query->whereTranslationLike("location", "%" . request("filters.location") . "%");
        }

        //filter by is_active
        if (request()->has("filters.is_active") and !empty(request("filters.is_active")) && request("filters.is_active") !== 'all') {
            $query->where("is_active", request("filters.is_active"));
        }

        //filter by rating
        
        if ((request()->has("filters.rating")) and !empty(request("filters.rating")) && request("filters.rating") != 'all') {
           
          $query->where("rating", request("filters.rating"));
        }

        //filter date on created_at
        if ((request()->has("filters.from_created_at")) and !empty(request("filters.from_created_at"))) {
            $query->whereDate("created_at", ">=", Carbon::parse(request("filters.from_created_at")));
        }

        if ((request()->has("filters.to_created_at")) and !empty(request("filters.to_created_at"))) {
            $query->whereDate("created_at", "<=", Carbon::parse(request("filters.to_created_at")));
        }

        //filter date on updated_at
        if ((request()->has("filters.from_updated_at")) and !empty(request("filters.from_updated_at"))) {
            $query->whereDate("updated_at", ">=", Carbon::parse(request("filters.from_updated_at")));
        }

        if ((request()->has("filters.to_updated_at")) and !empty(request("filters.to_updated_at"))) {
            $query->whereDate("updated_at", "<=", Carbon::parse(request("filters.to_updated_at")));
        }

        if (request()->has('trash') and request()->trash == 1) {
            $query->onlyTrashed();
        }
    }

    //end Scopes

    //start relations

    //end relations

    //start Attributes
    
    /**
     * Get the avatar/image URL
     */
    public function getImageUrlAttribute()
    {
        return MediaCenterHelper::getImageUrl($this->avatar);
    }

    /**
     * Alias for body - used in blade template as "content"
     */
    public function getContentAttribute()
    {
        return $this->body;
    }

    /**
     * Alias for title - used in blade template as "position"
     */
    public function getPositionAttribute()
    {
        return $this->title;
    }

    public function getActionsAttribute()
    {
        return $this->getActions('testimonials');
    }

    public function getItemsActionsAttribute()
    {
        return $this->getItemsActions('testimonials');
    }

    public function getShowActionsAttribute()
    {
        return $this->getShowActions('testimonials');
    }

    public function getItemDataAttribute()
    {
        return $this->getItemData('testimonials');
    }
    //end Attributes
}


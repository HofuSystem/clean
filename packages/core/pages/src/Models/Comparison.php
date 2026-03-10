<?php

namespace Core\Pages\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Pages\Observers\ComparisonObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use App\Observers\GlobalModelObserver;
use Carbon\Carbon;
use Core\Settings\Models\CoreModel;

#[ObservedBy([ComparisonObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Comparison extends CoreModel implements TranslatableContract
{
    use Translatable;

    protected $table = 'comparisons';
    protected $fillable = ['order', 'is_active', 'creator_id', 'updater_id'];
    protected $guarded = [];
    public $translatedAttributes = ["point", "us_text", "them_text"];

    //start Scopes
    function scopeSearch($query)
    {
        //filter text on point
        if ((request()->has("filters.point")) and !empty(request("filters.point"))) {
            $query->whereTranslationLike("point", "%" . request("filters.point") . "%");
        }

        //filter text on us_text
        if ((request()->has("filters.us_text")) and !empty(request("filters.us_text"))) {
            $query->whereTranslationLike("us_text", "%" . request("filters.us_text") . "%");
        }

        //filter text on them_text
        if ((request()->has("filters.them_text")) and !empty(request("filters.them_text"))) {
            $query->whereTranslationLike("them_text", "%" . request("filters.them_text") . "%");
        }

        //filter by is_active
        if (request()->has("filters.is_active") and request("filters.is_active") !== null) {
            $query->where("is_active", request("filters.is_active"));
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

    public function getActionsAttribute()
    {
        return $this->getActions('comparisons');
    }

    public function getItemsActionsAttribute()
    {
        return $this->getItemsActions('comparisons');
    }

    public function getShowActionsAttribute()
    {
        return $this->getShowActions('comparisons');
    }

    public function getItemDataAttribute()
    {
        return $this->getItemData('comparisons');
    }

    //end Attributes
}


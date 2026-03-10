<?php

namespace Core\Pages\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Pages\Observers\CounterObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use App\Observers\GlobalModelObserver;
use Carbon\Carbon;
use Core\Settings\Models\CoreModel;

#[ObservedBy([CounterObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Counter extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'counters';
	protected $fillable          = ['count', 'is_active', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["title"];

    //start Scopes
    function scopeSearch($query){

        //filter text on  title
        if((request()->has("filters.title")) and !empty(request("filters.title"))){
            $query->whereTranslationLike("title","%".request("filters.title")."%");
        }

        //filter by text on  count
        if((request()->has("filters.count")) and !empty(request("filters.count"))){
            $query->where("count", "like", "%".request("filters.count")."%");
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
      return $this->getActions('counters');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('counters');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('counters');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('counters');
    }
    //end Attributes

}

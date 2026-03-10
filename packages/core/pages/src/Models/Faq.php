<?php

namespace Core\Pages\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Pages\Observers\FaqObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use App\Observers\GlobalModelObserver;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;


#[ObservedBy([FaqObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Faq extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'faqs';
	protected $fillable          = ['slug', 'image', 'is_active', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["question","answer"];

    //start Scopes
    function scopeSearch($query){

        //filter text on  title
        if((request()->has("filters.question")) and !empty(request("filters.question"))){
            $query->whereTranslationLike("question","%".request("filters.question")."%");
        }

        //filter text on  answer
        if((request()->has("filters.answer")) and !empty(request("filters.answer"))){
            $query->whereTranslationLike("answer","%".request("filters.answer")."%");
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
      return $this->getActions('faqs');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('faqs');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('faqs');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('faqs');
    }
    //end Attributes

}

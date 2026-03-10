<?php

namespace Core\Categories\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Categories\Observers\CategorySettingObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;


#[ObservedBy([CategorySettingObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class CategorySetting extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'category_settings';
	protected $fillable          = ['slug', 'category_id', 'addon_price','cost' ,'parent_id', 'status', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["name"];

    //start Scopes
    function scopeSearch($query,$type = null){
        
        //filter text on  slug
        if((request()->has("filters.slug")) and !empty(request("filters.slug"))){
            $query->where("slug","LIKE","%".request("filters.slug")."%");
        }
        
        //filter text on  name
        if((request()->has("filters.name")) and !empty(request("filters.name"))){
            $query->whereTranslationLike("name","%".request("filters.name")."%");
        }
        
        //filter select on  category
        if((request()->has("filters.category_id")) and !empty(request("filters.category_id"))){
            $query->whereRelation("category","id",request("filters.category_id"));
        }
        
        //filter by number on  addon_price
        if((request()->has("filters.addon_price")) and !empty(request("filters.addon_price"))){
            $query->where("addon_price",request("filters.addon_price"));
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
        $query->when($type == 'category-settings',function($settingsQuery){
            $settingsQuery->whereNull('parent_id')->where(function($settingsQuery){
                $settingsQuery->whereNull('addon_price')->orWhere('addon_price',0);
            });
        })
        ->when($type == 'category-sub-settings',function($subCategoriesSettingsQuery){
            $subCategoriesSettingsQuery->whereNotNull('parent_id');
        })
        ->when($type == 'additional-features',function($servicesFeaturesQuery){
            $servicesFeaturesQuery->whereNull('parent_id')->whereNotNull('addon_price')->where('addon_price','>',0);
        });
        if(request()->has('trash') and request()->trash == 1){
            $query->onlyTrashed();
        }
    }
  
    //end Scopes

    //start relations
    
    public function category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function addonPrices(){
        return $this->morphMany(Price::class, 'priceable');
    }

    public function parent(){
        return $this->belongsTo(CategorySetting::class, 'parent_id', 'id');
    }

    public function categorySettings(){
        return $this->hasMany(CategorySetting::class, 'parent_id', 'id');
    }

    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
        $type       = in_array(request()->segment(2) , ['category-settings','category-sub-settings','additional-features']) ?  request()->segment(2) :  request()->segment(3);
        return $this->getActions($type);
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('category-settings');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('category-settings');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('category-settings');
    }
    //end Attributes

}

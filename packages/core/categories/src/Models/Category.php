<?php

namespace Core\Categories\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Info\Models\City;
use Core\Categories\Observers\CategoryObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Products\Models\Product;
use Core\Users\Models\User;

#[ObservedBy([CategoryObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Category extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'categories';
	protected $fillable          = ['slug', 'image', 'type', 'delivery_price','for_all_cities', 'sort', 'is_package', 'status', 'parent_id', 'city_id', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["name","intro","desc","meta_title","meta_description"];

    //start Scopes
    function scopeSearch($query,$type = null){

        //filter text on  name
        if((request()->has("filters.name")) and !empty(request("filters.name"))){
            $query->whereTranslationLike("name","%".request("filters.name")."%");
        }

        //filter text on  intro
        if((request()->has("filters.intro")) and !empty(request("filters.intro"))){
            $query->where("intro"."_".config("app.locale"),"LIKE","%".request("filters.intro")."%");
        }

        //filter text on  desc
        if((request()->has("filters.desc")) and !empty(request("filters.desc"))){
            $query->where("desc"."_".config("app.locale"),"LIKE","%".request("filters.desc")."%");
        }

        //filter select on  type
        if((request()->has("filters.type")) and !empty(request("filters.type"))){
            $query->where("type",request("filters.type"));
        }

        //filter by number on  sort
        if((request()->has("filters.sort")) and !empty(request("filters.sort"))){
            $query->where("sort",request("filters.sort"));
        }

        //filter select on  status
        if((request()->has("filters.status")) and !empty(request("filters.status"))){
            $query->where("status",request("filters.status"));
        }

        //filter select on  parent
        if((request()->has("filters.parent_id")) and !empty(request("filters.parent_id"))){
            $query->whereRelation("parent","id",request("filters.parent_id"));
        }

        //filter select on  city
        if((request()->has("filters.city_id")) and !empty(request("filters.city_id"))){
            $query->whereRelation("city","id",request("filters.city_id"));
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
        $query->when($type == 'categories',function($categoriesQuery){
            $categoriesQuery->whereIn('type',['clothes','sales','services'])->whereNull('parent_id');
        })
        ->when($type == 'sub-categories',function($subCategoriesQuery){
            $subCategoriesQuery->whereIn('type',['clothes','sales','services'])->whereNotNull('parent_id');
        })
        ->when($type == 'services',function($servicesQuery){
            $servicesQuery->whereIn('type',['maid','host'])->whereNull('parent_id');
        })
        ->when($type == 'sub-services',function($subServicesQuery){
            $subServicesQuery->whereIn('type',['maid','host'])->whereNotNull('parent_id');
        });
        if(request()->has('trash') and request()->trash == 1){
            $query->onlyTrashed();
        }
    }

    //end Scopes

    //start relations
    public function favers(){
        return $this->morphToMany(User::class, 'favs', 'favs', 'favs_id', 'user_id')
        ->where('favs_type', self::class);
    }
    public function products(){
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
    public function productsSub(){
        return $this->hasMany(Product::class, 'sub_category_id', 'id');
    }
    public function parent(){
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    public function cities(){
        return $this->belongsToMany(City::class,'category_cities');
    }

    public function subCategories(){
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function categoryTypes(){
        return $this->hasMany(CategoryType::class, 'category_id', 'id');
    }

    public function categorySettings(){
        return $this->hasMany(CategorySetting::class, 'category_id', 'id');
    }

    public function dateTimes(){
        return $this->hasMany(CategoryDateTime::class, 'category_id', 'id');
    }

    //end relations

    //start Attributes


    public function getActionsAttribute(){
        $slug  = in_array(request()->segment(2) , ['categories','sub-categories','services','sub-services']) ?  request()->segment(2) :  request()->segment(3);
        $slug = in_array($slug,['categories','sub-categories','services','sub-services']) ? $slug : 'categories';
        $actions = '<div class ="d-flex justify-content-center">';
        if(request()->has('trash') and request()->trash == 1){
            if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.restore')) {
                $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 btn-restore" href="' . route('dashboard.'.$slug.'.restore', ['id' => $this->id]) . '">
                    <i class="fa fa-trash-restore"></i> <span>' . trans('restore') . '</span>
                    </a>';
            }
            $actions .= '</div>';
            return $actions;
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.duplicate')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.'.$slug.'.duplicate', ['id' => $this->id]) . '">
                <i class="fa fa-copy"></i> <span>' . trans('duplicate') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.show')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.'.$slug.'.show', ['id' => $this->id]) . '">
                <i class="fa fa-eye"></i> <span>' . trans('show') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.edit')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.'.$slug.'.edit', ['id' => $this->id]) . '">
                <i class="fa fa-edit"></i> <span>' . trans('edit') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.delete')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 delete-btn" href="' . route('dashboard.'.$slug.'.delete', ['id' => $this->id]) . '">
                <i class="fa fa-trash"></i><span> ' . trans('delete') . ' </span>
                </a>';
        }

        $actions .= '</div>';
        return $actions;
    }
    public function getDescMobileAttribute(){
        $desc = $this->desc;
        if ($desc && strpos($desc, '<a') !== false) {
            $desc = preg_replace('/<a\b[^>]*>(.*?)<\/a>/is', '$1', $desc);
        }
     
        return $desc;
    }
    public function getImageUrlAttribute(){
        return MediaCenterHelper::getImagesUrl($this->image);
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('categories');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('categories');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('categories');
    }
    //end Attributes

}

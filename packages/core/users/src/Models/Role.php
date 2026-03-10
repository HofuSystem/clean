<?php

namespace Core\Users\Models;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\Users\Observers\RoleObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\Users\Scopes\UnderOperationRolesScope;

#[ObservedBy([RoleObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class Role extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'roles';
	protected $fillable          = ['name','guard_name', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["title"];

    //start Scopes
    function scopeSearch($query){
        
        //filter text on  title
        if((request()->has("filters.title")) and !empty(request("filters.title"))){
            $query->whereTranslationLike("title","%".request("filters.title")."%");
        }
        
        //filter text on  name
        if((request()->has("filters.name")) and !empty(request("filters.name"))){
            $query->where("name","LIKE","%".request("filters.name")."%");
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
    
    public function permissions(){
        //                                                           you many need to swap these last att
		return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
	}
    public function users()
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles');
    }
    //end relations

    //start Attributes
    
    public function getActionsAttribute(){
      return $this->getActions('roles');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('roles');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('roles');
    }

    public function getItemDataAttribute(){
        return $this->getItemData('roles');
    }
    //end Attributes
    protected static function booted()
    {
        static::addGlobalScope(new UnderOperationRolesScope());
    }
}

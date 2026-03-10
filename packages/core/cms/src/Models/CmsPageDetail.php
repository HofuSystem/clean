<?php

namespace Core\CMS\Models;
use Core\MediaCenter\Models\Media;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Core\CMS\Observers\CmsPageDetailObserver;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Core\Settings\Models\CoreModel;
use Carbon\Carbon;
use App\Observers\GlobalModelObserver;
use Core\MediaCenter\Helpers\MediaCenterHelper;

#[ObservedBy([CmsPageDetailObserver::class])]
#[ObservedBy([GlobalModelObserver::class])]

class CmsPageDetail extends CoreModel implements TranslatableContract{
    use Translatable;
	protected $table             = 'cms_page_details';
	protected $fillable          = ['image', 'tablet_image', 'mobile_image', 'icon', 'video', 'link', 'cms_pages_id', 'creator_id', 'updater_id'];
    protected $guarded           = [];
    public $translatedAttributes = ["name","description","intro","point"];

    //start Scopes
    function scopeSearch($query){
        $typeSlug =  request()->slug;
        if($typeSlug){
            $query->whereRelation("cmsPages","slug",$typeSlug);
        }
        //filter text on  name
        if((request()->has("filters.name")) and !empty(request("filters.name"))){
            $query->whereTranslationLike("name","%".request("filters.name")."%");
        }

        //filter text on  description
        if((request()->has("filters.description")) and !empty(request("filters.description"))){
            $query->where("description"."_".config("app.locale"),"LIKE","%".request("filters.description")."%");
        }

        //filter text on  intro
        if((request()->has("filters.intro")) and !empty(request("filters.intro"))){
            $query->where("intro"."_".config("app.locale"),"LIKE","%".request("filters.intro")."%");
        }

        //filter text on  point
        if((request()->has("filters.point")) and !empty(request("filters.point"))){
            $query->where("point"."_".config("app.locale"),"LIKE","%".request("filters.point")."%");
        }

        //filter text on  video
        if((request()->has("filters.video")) and !empty(request("filters.video"))){
            $query->where("video","LIKE","%".request("filters.video")."%");
        }

        //filter text on  link
        if((request()->has("filters.link")) and !empty(request("filters.link"))){
            $query->where("link","LIKE","%".request("filters.link")."%");
        }

        //filter select on  cmsPages
        if((request()->has("filters.cms_pages_id")) and !empty(request("filters.cms_pages_id"))){
            $query->whereRelation("cmsPages","id",request("filters.cms_pages_id"));
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

    public function cmsPages(){
        return $this->belongsTo(CmsPage::class, 'cms_pages_id', 'id');
    }

    //end relations

    //start Attributes
    public function getActions($slug)
    {
        $typeSlug =  request()->slug;
        $actions = '<div class ="d-flex justify-content-center">';
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.show')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.'.$slug.'.show', ['id' => $this->id,'slug'=>$typeSlug]) . '">
                <i class="fa fa-eye"></i> <span>' . trans('show') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.edit')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 " href="' . route('dashboard.'.$slug.'.edit', ['id' => $this->id,'slug'=>$typeSlug]) . '">
                <i class="fa fa-edit"></i> <span>' . trans('edit') . '</span>
                </a>';
        }
        if (auth('web')->check() and auth('web')->user()->can('dashboard.'.$slug.'.delete')) {
            $actions .= '<a class="btn-operation d-flex justify-content-center align-items-center mx-1 delete-btn" href="' . route('dashboard.'.$slug.'.delete', ['id' => $this->id,'slug'=>$typeSlug]) . '">
                <i class="fa fa-trash"></i><span> ' . trans('delete') . ' </span>
                </a>';
        }

        $actions .= '</div>';
        return $actions;
    }
    public function getActionsAttribute(){
      return $this->getActions('cms-page-details');
    }

    public function getItemsActionsAttribute(){
        return $this->getItemsActions('cms-page-details');
    }
    public function getShowActionsAttribute(){
        return $this->getShowActions('cms-page-details');
    }

    public function getItemDataAttribute(){
        $slug = 'cms-page-details';
        $typeSlug =  request()->slug;
        $data                    = method_exists($this, 'translations')  ? $this->load('translations')->toArray()  :$this->toArray();
        $data['translations']    = collect($data['translations'] ?? [] )->keyBy('locale')->toArray();
        $data['deleteUrl']       = route('dashboard.'.$slug.'.delete',['slug'=>$typeSlug,'id'=>$this->id]); 
        $data['updateUrl']       = route('dashboard.'.$slug.'.edit',['slug'=>$typeSlug,'id'=>$this->id]);
        return $data;
    }
    public function getImagePathAttribute()
    {
        return $this->image ? MediaCenterHelper::getUrl($this->image) : null;
    }

    public function getTabletImagePathAttribute()
    {
        return $this->tablet_image ? MediaCenterHelper::getUrl($this->tablet_image) : null;
    }

    public function getMobileImagePathAttribute()
    {
        return $this->mobile_image ? MediaCenterHelper::getUrl($this->mobile_image) : null;
    }

    public function getIconPathAttribute()
    {
        return $this->icon ? MediaCenterHelper::getUrl($this->icon) : null;
    }

    public function getVideoPathAttribute()
    {
        return $this->video ? MediaCenterHelper::getUrl($this->video) : null;
    }

    //end Attributes

}

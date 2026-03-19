<?php

namespace Core\B2B\Models;

use Core\Settings\Models\CoreModel;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use App\Observers\GlobalModelObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([GlobalModelObserver::class])]
class CompanyPermission extends CoreModel implements TranslatableContract
{
    use Translatable;

    protected $table = 'company_permissions';
    protected $fillable = ['slug', 'creator_id', 'updater_id'];
    public $translatedAttributes = ['name', 'description'];

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeSearch($query)
    {
        if (request()->has('filters.name') && !empty(request('filters.name'))) {
            $query->whereTranslationLike('name', '%' . request('filters.name') . '%');
        }
        if (request()->has('filters.slug') && !empty(request('filters.slug'))) {
            $query->where('slug', 'LIKE', '%' . request('filters.slug') . '%');
        }
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function employees()
    {
        return $this->hasMany(CompanyEmployee::class, 'permission_id', 'id');
    }

    // ─── Actions Attributes ───────────────────────────────────────────────────

    public function getActionsAttribute()
    {
        return $this->getActions('company-permissions');
    }

    public function getItemsActionsAttribute()
    {
        return $this->getItemsActions('company-permissions');
    }

    public function getShowActionsAttribute()
    {
        return $this->getShowActions('company-permissions');
    }

    public function getItemDataAttribute()
    {
        return $this->getItemData('company-permissions');
    }
}

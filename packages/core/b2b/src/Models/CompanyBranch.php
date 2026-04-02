<?php

namespace Core\B2B\Models;

use App\Observers\GlobalModelObserver;
use Carbon\Carbon;
use Core\B2B\Helpers\B2BHelper;
use Core\Info\Models\City;
use Core\Info\Models\District;
use Core\Settings\Models\CoreModel;
use Core\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Support\Facades\Auth;

#[ObservedBy([GlobalModelObserver::class])]

class CompanyBranch extends CoreModel
{
    protected $table    = 'company_branches';
    protected $fillable = ['name', 'location', 'lat', 'lng','is_active', 'city_id', 'district_id', 'user_id', 'is_default', 'company_id', 'creator_id', 'updater_id'];
    protected $guarded  = [];

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeSearch($query)
    {
        if (request()->has('filters.name') && !empty(request('filters.name'))) {
            $query->where('name', 'LIKE', '%' . request('filters.name') . '%');
        }
        if (request()->has('filters.company_id') && !empty(request('filters.company_id'))) {
            $query->where('company_id', request('filters.company_id'));
        }
        if (request()->has('filters.from_created_at') && !empty(request('filters.from_created_at'))) {
            $query->whereDate('created_at', '>=', Carbon::parse(request('filters.from_created_at')));
        }
        if (request()->has('filters.to_created_at') && !empty(request('filters.to_created_at'))) {
            $query->whereDate('created_at', '<=', Carbon::parse(request('filters.to_created_at')));
        }
        if (request()->has('trash') && request()->trash == 1) {
            $query->onlyTrashed();
        }
    }

    public function scopeUnderManagement($query)
    {
        
    }
    public function scopeB2bUnderManagement($query,$permission)
    {
        $branchIds = B2BHelper::getB2BBranchIds($permission);
        $query->whereIn('company_branches.id', $branchIds);
        return $query;
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // ─── Actions Attributes ───────────────────────────────────────────────────

    public function getActionsAttribute()
    {
        return $this->getActions('company-branches');
    }

    public function getItemsActionsAttribute()
    {
        return $this->getItemsActions('company-branches');
    }

    public function getShowActionsAttribute()
    {
        return $this->getShowActions('company-branches');
    }

    public function getItemDataAttribute()
    {
        return $this->getItemData('company-branches');
    }
}

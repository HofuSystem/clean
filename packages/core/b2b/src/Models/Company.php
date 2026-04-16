<?php

namespace Core\B2B\Models;

use Carbon\Carbon;
use Core\Users\Models\User;
use App\Observers\GlobalModelObserver;
use Core\Settings\Models\CoreModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([GlobalModelObserver::class])]

class Company extends CoreModel
{
    protected $table    = 'companies';
    protected $fillable = ['fullname', 'name_ar', 'name_en', 'street_name', 'building_no', 'district_id', 'postal_code', 'additional_number', 'city_id', 'line_of_business', 'email', 'phone', 'bank_account_number', 'iban', 'commercial_registration', 'tax_number', 'image', 'owner_id', 'is_active', 'creator_id', 'updater_id'];
    protected $guarded  = [];

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeSearch($query)
    {
        if (request()->has('filters.fullname') && !empty(request('filters.fullname'))) {
            $query->where('fullname', 'LIKE', '%' . request('filters.fullname') . '%');
        }
        if (request()->has('filters.email') && !empty(request('filters.email'))) {
            $query->where('email', 'LIKE', '%' . request('filters.email') . '%');
        }
        if (request()->has('filters.phone') && !empty(request('filters.phone'))) {
            $query->where('phone', 'LIKE', '%' . request('filters.phone') . '%');
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

    // ─── Relations ────────────────────────────────────────────────────────────

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function branches()
    {
        return $this->hasMany(CompanyBranch::class, 'company_id', 'id');
    }

    public function financials()
    {
        return $this->hasMany(B2BFinancial::class, 'company_id', 'id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'company_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(\Core\Info\Models\City::class, 'city_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(\Core\Info\Models\District::class, 'district_id', 'id');
    }

    // ─── Actions Attributes ───────────────────────────────────────────────────

    public function getActionsAttribute()
    {
        $actions = $this->getActions('companies');
        
        $statementUrl = route('dashboard.company-statement.show', $this->id);
        $btn = '<a class="btn-operation d-flex justify-content-center align-items-center mx-1" href="' . $statementUrl . '" title="' . trans('Statement') . '">
                    <i class="fas fa-file-invoice"></i> <span>' . trans('Statement') . '</span>
                </a>';
        
        return preg_replace('/<\/div>$/', $btn . '</div>', $actions);
    }

    public function getItemsActionsAttribute()
    {
        return $this->getItemsActions('companies');
    }

    public function getShowActionsAttribute()
    {
        return $this->getShowActions('companies');
    }

    public function getItemDataAttribute()
    {
        return $this->getItemData('companies');
    }
}
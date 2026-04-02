<?php

namespace Core\B2B\Models;

use Core\Settings\Models\CoreModel;
use Core\Users\Models\User;
use App\Observers\GlobalModelObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([GlobalModelObserver::class])]
class CompanyEmployee extends CoreModel
{
    protected $table = 'company_employees';
    protected $fillable = ['user_id', 'company_id', 'permission_id', 'branch_id'];

    // ─── Attributes ───────────────────────────────────────────────────────────

    public function getItemDataAttribute()
    {
        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'permission_id' => $this->permission_id,
            'branch_id'     => $this->branch_id,
            'user'          => $this->user?->fullname,
            'permission'    => $this->permission?->name,
            'branch'        => $this->branch?->name ?? trans('All Branches'),
        ];
    }

    public function getItemsActionsAttribute()
    {
        $actions = '<div class ="d-flex justify-content-center">';
        $actions .= ' <button class="btn btn-icon btn-light-primary btn-sm edit-item mx-1" data-href="' . route('dashboard.company-employees.edit', ['id' => $this->id]) . '"><i class="fas fa-pencil-alt"></i></button>';
        $actions .= '<button class="btn btn-icon btn-light-danger btn-sm delete-item mx-1" data-href="' . route('dashboard.company-employees.delete', ['id' => $this->id]) . '"> <i class="fas fa-trash"></i></button>';
        $actions .= '</div>';
        return $actions;
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function permission()
    {
        return $this->belongsTo(CompanyPermission::class, 'permission_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(CompanyBranch::class, 'branch_id', 'id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeSearch($query)
    {
        if (request()->has('filters.user_id') && !empty(request('filters.user_id'))) {
            $query->where('user_id', request('filters.user_id'));
        }
        if (request()->has('filters.company_id') && !empty(request('filters.company_id'))) {
            $query->where('company_id', request('filters.company_id'));
        }
    }
}

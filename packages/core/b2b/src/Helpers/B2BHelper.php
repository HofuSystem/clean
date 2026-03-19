<?php

namespace Core\B2B\Helpers;

use Core\B2B\Models\Company;
use Core\B2B\Models\CompanyEmployee;
use Core\B2B\Models\Contract;
use Illuminate\Support\Facades\Auth;

class B2BHelper
{
    /**
     * Checks if the current B2B user has the required permission slug.
     */
    public static function hasPermission(string $permissionSlug): bool
    {
        $role = request()->attributes->get('b2b_role');
        
        if ($role === 'owner') {
            return true;
        }
        $hasPermission = CompanyEmployee::where('user_id', Auth::id())
        ->whereHas('permission', function ($query) use ($permissionSlug) {
            $query->where('slug', $permissionSlug);
        })->exists();
        
        return $hasPermission;
    }
    
    /**
     * Aborts with a 403 error if the current user doesn't have permission.
     */
    public static function checkPermission(string $permissionSlug)
    {
        if (!self::hasPermission($permissionSlug)) {
            abort(403, 'Unauthorized. You lack the permission: ' . $permissionSlug);
        }
    }
    

    /**
     * Automatically populate company_id, branch_id, and correct client_id onto incoming order/invoice creations.
     */
    public static function getCreationContext()
    {
        $role = request()->attributes->get('b2b_role');
        $companyId = request()->attributes->get('b2b_company_id');
        $company = Company::find($companyId);
        $user = Auth::guard('web')->user() ?? Auth::user();
        
        $context = [
            'role' => $role,
            'company_id' => $company->id,
            'client_id' => $company->owner_id,
            'branch_id' => null,
        ];
        
       
        return $context;
    }
    
 
    public static function getB2BCompanyId()
    {
        return self::getCreationContext()['company_id'];
    }
    public static function getB2BBranchIds($permission)
    {
        $role = request()->attributes->get('b2b_role');
        $comapnyId = request()->attributes->get('b2b_company_id');
        if($role == 'owner'){
            return Company::find($comapnyId)->branches()->pluck('id')->toArray();
        }
        if($role == 'manager'){
            $userId = Auth::user()->id;
            $isTopManager = CompanyEmployee::where('company_id', $comapnyId)
            ->where('user_id', $userId)
            ->whereHas('permission', function ($query) use ($permission) {
                $query->where('slug', $permission);
            })
            ->doesntHave('branch')
            ->exists();
            if($isTopManager){
                return Company::find($comapnyId)->branches()->pluck('id')->toArray();
            }
            $isBranchManager = CompanyEmployee::where('company_id', $comapnyId)
            ->where('user_id', $userId)
            ->whereHas('permission', function ($query) use ($permission) {
                $query->where('slug', $permission);
            })
            ->whereHas('branch')
            ->pluck('branch_id')->toArray();
            if($isBranchManager){
                return $isBranchManager;
            }
        }
        return [];
    }
}

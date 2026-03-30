<?php

namespace Core\B2B\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Core\B2B\Models\CompanyEmployee;
use Core\B2B\Models\CompanyPermission;
use Core\B2B\Models\CompanyBranch;
use Core\Users\Models\User;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Core\B2B\Helpers\B2BHelper;
use Core\B2B\Requests\FrontEnd\StoreEmployeeRequest;
use Core\B2B\Requests\FrontEnd\UpdateEmployeeRequest;
use Core\B2B\Requests\FrontEnd\UpdateEmployeePasswordRequest;

class EmployeeController extends Controller
{
    use ApiResponse;

    public function index()
    {
        B2BHelper::checkPermission('manage-user-permissions');
        $title = trans('client.employees');
        $description = trans('client.employees_description');
        
        $b2bContext = B2BHelper::getCreationContext();
        $companyId = $b2bContext['company_id'];
        
        $employees = CompanyEmployee::where('company_id', $companyId)
            ->with(['user', 'permission', 'branch'])
            ->get();
            
        $permissions = CompanyPermission::all();
        $branches = CompanyBranch::where('company_id', $companyId)->get();

        return view('b2b::web.pages.role-management', compact('employees', 'permissions', 'branches', 'title', 'description'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        B2BHelper::checkPermission('manage-user-permissions');
        
        try {
            $b2bContext = B2BHelper::getCreationContext();
            $companyId = $b2bContext['company_id'];
            
            // Validate branch belongs to company
            if ($request->branch_id) {
                $branch = CompanyBranch::where('id', $request->branch_id)->where('company_id', $companyId)->first();
                if (!$branch) {
                    return back()->withErrors(['error' => trans('client.invalid_branch')])->withInput();
                }
            }

            // Find or create user
            $user = User::withTrashed()->where('phone', $request->phone)->first();
            
            if ($user && $user->deleted_at) {
                $user->restore();
            }

            if (!$user) {
                $user = User::create([
                    'phone' => $request->phone,
                    'fullname' => $request->fullname,
                    'is_active' => true,
                    'password' => $request->password ?: $request->phone,
                ]);
            }

            // Check if already an employee
            $existing = CompanyEmployee::where('company_id', $companyId)
                ->where('user_id', $user->id)
                ->first();

            if ($existing) {
                return back()->withErrors(['error' => trans('client.employee_already_exists')])->withInput();
            }

            $branchIds = $request->branch_ids ?? [null];
            foreach ($branchIds as $branchId) {
                foreach ($request->permission_ids as $permissionId) {
                    CompanyEmployee::create([
                        'user_id' => $user->id,
                        'company_id' => $companyId,
                        'permission_id' => $permissionId,
                        'branch_id' => $branchId,
                    ]);
                }
            }

            return redirect()->route('client.employees.index')->with('success', trans('client.employee_added_success'));

        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.employee_add_failed')])->withInput();
        }
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        B2BHelper::checkPermission('manage-user-permissions');
        
        try {
            $b2bContext = B2BHelper::getCreationContext();
            $companyId = $b2bContext['company_id'];
            
            $employee = CompanyEmployee::where('company_id', $companyId)->where('user_id', $id)->firstOrFail();

            // Validate branch belongs to company
            if ($request->branch_id) {
                $branch = CompanyBranch::where('id', $request->branch_id)->where('company_id', $companyId)->first();
                if (!$branch) {
                    return back()->withErrors(['error' => trans('client.invalid_branch')])->withInput();
                }
            }

            // Remove existing permissions for this user to re-assign
            CompanyEmployee::where('company_id', $companyId)
                ->where('user_id', $id)
                ->delete();

            $branchIds = $request->branch_ids ?? [null];
            foreach ($branchIds as $branchId) {
                foreach ($request->permission_ids as $permissionId) {
                    CompanyEmployee::create([
                        'user_id' => $id,
                        'company_id' => $companyId,
                        'permission_id' => $permissionId,
                        'branch_id' => $branchId,
                    ]);
                }
            }

            return redirect()->route('client.employees.index')->with('success', trans('client.employee_updated_success'));

        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.employee_update_failed')])->withInput();
        }
    }

    public function destroy($id)
    {
        B2BHelper::checkPermission('manage-user-permissions');
        
        try {
            $b2bContext = B2BHelper::getCreationContext();
            $companyId = $b2bContext['company_id'];
            
            $employeePermissions = CompanyEmployee::where('company_id', $companyId)->where('user_id', $id)->get();
            
            if ($id == Auth::id()) {
                return back()->withErrors(['error' => trans('client.cannot_delete_self')]);
            }
            
            CompanyEmployee::where('company_id', $companyId)->where('user_id', $id)->delete();

            return redirect()->route('client.employees.index')->with('success', trans('client.employee_deleted_success'));

        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.employee_delete_failed')]);
        }
    }

    public function updatePassword(UpdateEmployeePasswordRequest $request, $id)
    {
        B2BHelper::checkPermission('manage-user-permissions');
        
        try {
            $b2bContext = B2BHelper::getCreationContext();
            $companyId = $b2bContext['company_id'];
            
            // Security check: Employee must belong to the same company
            CompanyEmployee::where('company_id', $companyId)->where('user_id', $id)->firstOrFail();
            
            $user = User::findOrFail($id);
            $user->update(['password' => $request->password]);
            
            return back()->with('success', trans('client.employee_password_updated_success'));
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('client.employee_password_update_failed')]);
        }
    }
}

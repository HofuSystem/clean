<?php

namespace Core\B2B\Middleware;

use Closure;
use Illuminate\Http\Request;
use Core\B2B\Models\Company;
use Core\B2B\Models\Contract;
use Core\B2B\Models\CompanyEmployee;
use Illuminate\Support\Facades\Auth;

class IsB2BClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('web')->user() ?? Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized. Please login.');
        }

        $isCompanyOwner = Company::where('owner_id', $user->id)->first();
        $employeeRecord = CompanyEmployee::where('user_id', $user->id)->with('permission')->first();
        if ($isCompanyOwner) {
            $request->attributes->set('b2b_role', 'owner');
            $request->attributes->set('b2b_company_id', $isCompanyOwner->id);
        } elseif ($employeeRecord) {
            $request->attributes->set('b2b_role', 'manager');
            $request->attributes->set('b2b_company_id', $employeeRecord->company_id);
        } else {
            abort(403, 'Unauthorized. You are not a B2B Client or Employee.');
        }

        return $next($request);
    }
}

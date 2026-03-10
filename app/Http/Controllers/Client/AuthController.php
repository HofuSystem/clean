<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\LoginRequest;
use App\Http\Requests\Client\RegisterRequest;
use Core\Users\Models\Role;
use Core\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function register()
    {
        $title = trans('auth.register_title');
        return view('auth.register', compact('title'));
    }

    public function login()
    {
        $title = trans('auth.login_title');
        return view('auth.login', compact('title'));
    }

    public function registerStore(RegisterRequest $request)
    {
        try {
            // Create user with inactive status
            $user = User::create([
                'fullname' => $request->company_name . ' ' . $request->contact_person . ' ' . $request->monthly_items,
                'phone' => $request->phone,
                'password' => \Str::random(10),
                'business_field' => $request->type,
                'is_active' => false, // Set user as inactive
            ]);
            $role = Role::withTrashed()->where('name', 'company')->first();
            if($role->trashed()){
                $role->restore();
            }
            if (!$role) {
                $role = Role::updateOrCreate(['name' => 'company'], [
                    'name' => 'company',
                    'guard_name' => 'api',
                    'translations' => [
                        'ar' => [
                            'title' => 'الشركة',
                        ],
                        'en' => [
                            'title' => 'Company',
                        ],
                    ],
                ]);
            }
            $user->roles()->attach($role->id);
            session()->flash('success_message', trans('auth.account_review_message'));
            return redirect()->route('home')->with('success', trans('auth.account_review_message'));
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('auth.registration_failed')])->withInput();
        }
    }

    public function loginStore(LoginRequest $request)
    {
        try {
            // Find user by phone
            $user = User::where('phone', $request->phone)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'company');
                })->first();
            if (!$user) {
                return back()->withErrors(['phone' => trans('auth.failed')])->withInput();
            }
            if (!$user->is_active) {
                return back()->withErrors(['phone' => trans('auth.account_not_active')])->withInput();
            }


            // Check if password is correct
            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors(['password' => trans('auth.failed')])->withInput();
            }

            // Check if user is active
            if (!$user->is_active) {
                return back()->withErrors(['phone' => trans('auth.account_not_active')])->withInput();
            }

            // Log the user in
            Auth::login($user);

            // Update last login
            $user->update(['last_login_at' => now()]);

            return redirect()->route('client.dashboard')->with('success', trans('auth.login_success'));
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => trans('auth.login_failed')])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', trans('auth.logout_success'));
    }
}

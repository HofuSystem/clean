<?php

use App\Http\Controllers\Client\AddressController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PageController;
use Core\Orders\Services\OrderTransactionsService;
use Core\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Client\ScheduleController;
use Carbon\Carbon;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderTransaction;
use Core\Settings\Helpers\ToolHelper;

Route::get('/test-migration', function () {
    $stage = request('stage', 0);

    // Stage 0: Identification & Dry Run
    $query = Core\Users\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'company');
        }
        )
            ->whereHas('addresses')
            ->whereHas('orders')
            ->with(['addresses', 'contract']);

        // Stage 1: Create Companies and Link Contracts
        if ($stage == 1) {
            $users = $query->get();
            $count = 0;
            foreach ($users as $user) {
                $company = Core\B2B\Models\Company::firstOrCreate(
                ['owner_id' => $user->id],
                [
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'line_of_business' => $user->business_field,
                    'is_active' => true,
                    'creator_id' => $user->id,
                ]
                );

                // Update User's company_id
                $user->update(['company_id' => $company->id]);

                // Link Contract
                if ($user->contract) {
                    $user->contract->update(['company_id' => $company->id]);
                }
            }
            return;
        }

        // Stage 2: Create Branches and Link Orders
        if ($stage == 2) {
            echo "<h2>Stage 2: Creating Branches & Updating Orders</h2>";
            $users = $query->get();
            $count = 0;
            $orderUpdates = 0;
            foreach ($users as $user) {
                $company = Core\B2B\Models\Company::where('owner_id', $user->id)->first();
                if (!$company) {
                    echo "Skipping user: {$user->fullname} (Company not found, run Stage 1 first)<br>";
                    continue;
                }

                $address = $user->addresses->first();
                if ($address) {
                    $branch = Core\B2B\Models\CompanyBranch::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'name' => $address->name ?? $user->fullname . " Branch",
                    ],
                    [
                        'location' => $address->location,
                        'lat' => $address->lat,
                        'lng' => $address->lng,
                        'city_id' => $address->city_id,
                        'district_id' => $address->district_id,
                        'user_id' => $user->id,
                        'is_active' => true,
                        'is_default' => true,
                    ]
                    );

                    // Loop on client orders and update them
                    $updated = Core\Orders\Models\Order::where('client_id', $user->id)
                    ->update([
                        'company_id' => $company->id,
                        'b2b_type' => 'company',
                        'branch_id' => $branch->id,
                        
                    ]);

                    echo "Processed user: {$user->fullname} (Branch created, $updated orders updated)<br>";
                    $orderUpdates += $updated;
                    $count++;
                }
            }
            echo "<br>Finished Stage 2. Processed $count branches and $orderUpdates orders.<br>";
            return "Migration Step 2 complete.";
        }

        // Stage 3: Update OrderSchedules
        if ($stage == 3) {
            echo "<h2>Stage 3: Updating OrderSchedules</h2>";
            $users = $query->get();
            $count = 0;
            $scheduleUpdates = 0;
            foreach ($users as $user) {
                $company = Core\B2B\Models\Company::where('owner_id', $user->id)->first();
                if (!$company) {
                    echo "Skipping user: {$user->fullname} (Company not found, run Stage 1 first)<br>";
                    continue;
                }

                $branch = Core\B2B\Models\CompanyBranch::where('company_id', $company->id)->first();
                if (!$branch) {
                    echo "Skipping user: {$user->fullname} (Branch not found, run Stage 2 first)<br>";
                    continue;
                }

                // Update OrderSchedules
                $updated = Core\Orders\Models\OrderSchedule::where('client_id', $user->id)
                    ->update([
                        'company_id' => $company->id,
                        'branch_id' => $branch->id,
                    ]);

                echo "Processed user: {$user->fullname} ($updated schedules updated)<br>";
                $scheduleUpdates += $updated;
                $count++;
            }
            echo "<br>Finished Stage 3. Processed $count users and updated $scheduleUpdates schedules.<br>";
            return "Migration Step 3 complete.";
        }

    });
Route::group(
[
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
],
        function () {

        Route::get('', [PageController::class , 'home'])->name('home');
        Route::get('/b2b', [PageController::class , 'b2b'])->name('b2b');
        Route::get('/blogs', [PageController::class , 'blog'])->name('blog');
        Route::get('/blogs/{slug}', [PageController::class , 'blogPost'])->name('blogs-single');
        Route::get('/services', [PageController::class , 'services'])->name('services');
        Route::get('/contact-us', [PageController::class , 'contactUs'])->name('contact');
        Route::post('/contact-us', [PageController::class , 'contactUsRequest'])->name('contact');
        Route::get('/faq', [PageController::class , 'faq'])->name('faq');
        Route::get('/why-us', [PageController::class , 'whyUs'])->name('why-us');
        Route::get('/app-features', [PageController::class , 'appFeatures'])->name('app.features');
        Route::get('/testimonials', [PageController::class , 'testimonials'])->name('testimonials');


        Route::get('/terms', [PageController::class , 'terms'])->name('terms');
        Route::get('/privacy', [PageController::class , 'privacy'])->name('privacy');
        Route::get('/payment-gateway', [PageController::class , 'paymentGateway'])->name('payment-gateway');
        Route::view('/social', 'social')->name('social');
        Route::view('/allInfo', 'allInfo')->name('allInfo');
        Route::get('sitemap.xml', [PageController::class , 'siteMap']);
        Route::post('/newsletter', function () {
            DB::table('news_letters')
                ->updateOrInsert(
            ['email' => request('email')],
            ['updated_at' => now(), 'created_at' => now()]
            );
            return redirect()->back()->with('success', 'Thank you for subscribing to our newsletter');
        }
        )->name('newsletter');



    });
<?php

namespace Database\Seeders;

use Core\MediaCenter\Models\Media;
use Core\Settings\Helpers\ToolHelper;
use Core\Users\Models\Role;
use Core\Users\Models\RoleTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateDataSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
      DB::transaction(function () {
         /////////////////////////////////////////
         $db_countries = DB::connection('mysql2')->table('countries')->get();
         foreach ($db_countries as $db_country) {
            $trans            =  DB::connection('mysql2')->table('country_translations')->where('locale', 'en')->where('country_id', $db_country->id)->first();
            $db_country->slug =  \Str::slug($trans->name);
            if (\Core\Info\Models\Country::withTrashed()->orWhere('slug', $db_country->slug)->orWhere('short_name', $db_country->short_name)->exists()) {
               $db_country->slug =  $trans->name . \Str::random(5);
            }

            $country = \Core\Info\Models\Country::insert([
               'id' =>  $db_country->id,
               'slug' => $db_country->slug,
               'phonecode' => $db_country->phonecode,
               'short_name' => $db_country->short_name ?? $db_country->slug,
               'flag' => $db_country->flag,
               'created_at' => $db_country->created_at,
               'updated_at' => $db_country->updated_at,
               'deleted_at' => $db_country?->deleted_at,

            ]);
         }

         $db_countries_translations = DB::connection('mysql2')->table('country_translations')->get();
         foreach ($db_countries_translations as $db_country_translations) {
            \Core\Info\Models\CountryTranslation::insert([
               'id'           => $db_country_translations->id,
               'name'         => $db_country_translations->name,
               'country_id'   => $db_country_translations->country_id,
               'locale'       => $db_country_translations->locale ?? 'ar',

            ]);
         }

         //////////////////////////////////////////
         $db_cities = DB::connection('mysql2')->table('cities')->get();
         foreach ($db_cities as $db_city) {
            $trans            =  DB::connection('mysql2')->table('city_translations')->where('locale', 'en')->where('city_id', $db_city->id)->first();
            $db_city->slug    =  \Str::slug($trans->name);
            if (\Core\Info\Models\City::withTrashed()->where('slug', $db_city->slug)->exists()) {
               $db_city->slug =  $db_city->slug . '-' . \Str::random(5);
            }
            try {
               //code...
               $city = \Core\Info\Models\City::insert([
                  'id' =>  $db_city->id,
                  'slug' => $db_city->slug,
                  'lat' => $db_city->lat,
                  'lng' => $db_city->lng,
                  'postal_code' => $db_city->postal_code,
                  'status' => 'active',
                  'country_id' => $db_city->country_id,
                  'created_at' => $db_city->created_at,
                  'updated_at' => $db_city->updated_at,
                  'deleted_at' => $db_city?->deleted_at,

               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }

         $db_cities_translations = DB::connection('mysql2')->table('city_translations')->get();
         foreach ($db_cities_translations as $db_city_translations) {
            try {
               \Core\Info\Models\CityTranslation::insert([
                  'name'      => $db_city_translations->name,
                  'city_id'   => $db_city_translations->city_id,
                  'locale'    => $db_city_translations->locale,

               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }
         $skipDistricts = [57,36,59];
         $districtIds   = [];
         //////////////////////////////////////////
         $db_districts = DB::connection('mysql2')->table('districts')->get();
         foreach ($db_districts as $db_district) {
            $trans            =  DB::connection('mysql2')->table('district_translations')->where('locale', 'ar')->where('district_id', $db_district->id)->first();
            
            $db_district->slug    =  \Str::replace('حى ',"",$trans->name);
            if (\Core\Info\Models\District::withTrashed()->orWhere('slug', $db_district->slug)->exists()) {
               $db_district->slug =  $trans->name . \Str::random(5);
            }
            if(in_array($db_district->id,$skipDistricts)){
               $districtIds[$db_district->id] = null;
               continue;
            }
            if($db_district->id == 76){
               $districtIds[$db_district->id] = 70;
               continue;
            }
            if($db_district->id == 29){
               $districtIds[$db_district->id] = 14;
               continue;
            }
            if($db_district->id == 55){
               $districtIds[$db_district->id] = 12;
               continue;
            }
            if($db_district->id == 62){
               $districtIds[$db_district->id] = 52;
               continue;
            }
            if($db_district->id == 39){
               $districtIds[$db_district->id] = 31;
               continue;
            }
            if($db_district->id == 37){
               $districtIds[$db_district->id] = 30;
               continue;
            }
            if($db_district->id == 64){
               $districtIds[$db_district->id] = 44;
               continue;
            }
            try {
               $district = \Core\Info\Models\District::insert([
                  'id'           =>  $db_district->id,
                  'slug'         => $db_district->slug,
                  'lat'          => $db_district->lat,
                  'lng'          => $db_district->lng,
                  'postal_code'  => $db_district->postal_code,
                  'city_id'      => $db_district->city_id,
                  'created_at' => $db_district->created_at,
                  'updated_at' => $db_district->updated_at,
                  'deleted_at' => $db_district?->deleted_at,
               ]);
               
               $districtIds[$db_district->id] = $db_district->id;
            } catch (\Throwable $th) {
               dd($th);
            }
         }

         $db_districts_translations = DB::connection('mysql2')->table('district_translations')->get();
         foreach ($db_districts_translations as $db_district_translations) {
            try {
               \Core\Info\Models\DistrictTranslation::insert([
                  'name' => $db_district_translations->name,
                  'district_id' => $db_district_translations->district_id,
                  'locale' => $db_district_translations->locale,

               ]);
            } catch (\Throwable $th) {
               //throw $th;
               // dd($th);
            }
         }
         //////////////////////////////////////////

         $db_nationalities = DB::connection('mysql2')->table('nationalities')->get();
         foreach ($db_nationalities as $db_nationality) {
            $nationality      = \Core\Info\Models\Nationality::createQuietly([
               'id'           =>  $db_nationality->id,
               'arranging'    => $db_nationality->arranging,
               'created_at' => $db_nationality->created_at,
               'updated_at' => $db_nationality->updated_at,
               'deleted_at' => @$db_nationality?->deleted_at,
            ]);
            \Core\Info\Models\NationalityTranslation::insert([
                  'nationality_id' => $nationality->id,
                  'locale' => 'en',
                  'name' => $db_nationality->name_en,
            ]);
            \Core\Info\Models\NationalityTranslation::insert([
                  'nationality_id' => $nationality->id,
                  'locale' => 'ar',
                  'name' => $db_nationality->name_ar,
            ]);
         }

         //////////////////////////////////////////
         $db_cmsPages = DB::connection('mysql2')->table('cms_pages')->get();
         foreach ($db_cmsPages as $db_cmsPage) {

            if (\Core\CMS\Models\CmsPage::withTrashed()->where('slug', $db_cmsPage->slug)->exists()) {
               $db_cmsPage->slug = $db_cmsPage->slug . '-' . \Str::random(5);
            }

            $cmsPage = \Core\CMS\Models\CmsPage::firstOrCreate(['id'  =>  $db_cmsPage->id], [
               'slug' => $db_cmsPage->slug,
               'is_parent' => $db_cmsPage->is_parent,
               'is_multi_upload' => $db_cmsPage->is_multi_upload,
               'have_point' => $db_cmsPage->have_point,
               'have_name' => $db_cmsPage->have_name,
               'have_description' => $db_cmsPage->have_description,
               'have_intro' => $db_cmsPage->have_intro,
               'have_image' => $db_cmsPage->have_image,
               'have_tablet_image' => $db_cmsPage->have_tablet_image,
               'have_mobile_image' => $db_cmsPage->have_mobile_image,
               'have_icon' => $db_cmsPage->have_icon,
               'have_video' => $db_cmsPage->have_video,
               'have_link' => $db_cmsPage->have_link,
              
               'created_at' => $db_cmsPage->created_at,
               'updated_at' => $db_cmsPage->updated_at,
               'deleted_at' => @$db_cmsPage?->deleted_at,
            ]);
            \Core\CMS\Models\CmsPageTranslation::insert([
               'cms_page_id' => $cmsPage->id,
               'locale' => 'en',
               'name' => $db_cmsPage->name_en,
            ]);
            \Core\CMS\Models\CmsPageTranslation::insert([
               'cms_page_id' => $cmsPage->id,
               'locale' => 'ar',
               'name' => $db_cmsPage->name_ar,
            ]);
          
         }


         //////////////////////////////////////////


         $db_cmsPageDetails = DB::connection('mysql2')->table('cms_page_details')->get();
         foreach ($db_cmsPageDetails as $db_cmsPageDetail) {
            try {
               //code...
               $cmsPageDetail = \Core\CMS\Models\CmsPageDetail::createQuietly([
                  'id' =>  $db_cmsPageDetail->id,
                  'image' => $db_cmsPageDetail->image,
                  'tablet_image' => $db_cmsPageDetail->tablet_image,
                  'mobile_image' => $db_cmsPageDetail->mobile_image,
                  'icon' => $db_cmsPageDetail->icon,
                  'video' => $db_cmsPageDetail->video,
                  'link' => $db_cmsPageDetail->link,
                  'cms_pages_id' => $db_cmsPageDetail->cms_pages_id,
                 
                  'created_at' => $db_cmsPageDetail->created_at,
                  'updated_at' => $db_cmsPageDetail->updated_at,
                  'deleted_at' => @$db_cmsPageDetail?->deleted_at,
               ]);
               \Core\CMS\Models\CmsPageDetailTranslation::insert([
                  'cms_page_detail_id' => $cmsPageDetail->id,
                  'locale'             => 'en',
                  'name'               => $db_cmsPageDetail->name_en,
                  'description'        => $db_cmsPageDetail->description_en,
               ]);
               \Core\CMS\Models\CmsPageDetailTranslation::insert([
                  'cms_page_detail_id' => $cmsPageDetail->id,
                  'locale'             => 'ar',
                  'name'               => $db_cmsPageDetail->name_ar,
                  'description'        => $db_cmsPageDetail->description_ar,
               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }


         //////////////////////////////////////////

         $db_users = DB::connection('mysql2')->table('users')->get();
         foreach ($db_users as $db_user) {

            if (\Core\Users\Models\User::withTrashed()->whereNotNull('email')->where('email', $db_user->email)->exists()) {
               $db_user->email = 'nq-' . $db_user->id . '-' . $db_user->email;
            }
            if (\Core\Users\Models\User::withTrashed()->whereNotNull('phone')->where('phone', $db_user->phone)->exists()) {
               $db_user->phone = 'nq-' . $db_user->id . '-' . $db_user->phone;
            }


            $user = DB::table('users')->insert([
               'id' =>  $db_user->id,
               'fullname' => $db_user->fullname,
               'email' => $db_user->email,
               'email_verified_at' => $db_user->email_verified_at,
               'phone' => $db_user->phone,
               'phone_verified_at' => $db_user->phone_verified_at,
               'is_active' => $db_user->is_active,
               'is_allow_notify' => $db_user->is_allow_notify,
               'date_of_birth' => $db_user->date_of_birth,
               'identity_number' => $db_user->identity_number,
               'wallet' => $db_user->wallet ?? 0,
               'gender' => $db_user->gender ?? "male",
               'rate_avg' => $db_user->rate_avg,
               'referral_code' => $db_user->referral_code,
               'verified_code' => $db_user->verified_code,
               'created_at' => $db_user->created_at,
               'updated_at' => $db_user->updated_at,
               'deleted_at' => @$db_user->deleted_at,
            ]);
            if (isset($db_user->user_type) and !empty($db_user->user_type)) {
               $role = Role::firstOrCreate(['name' => $db_user->user_type, 'guard_name' => 'web']);
              
               DB::table('model_has_roles')->insert([
                  'role_id'      => $role->id,
                  'model_type'   => \Core\Users\Models\User::class,
                  'model_id'     => $db_user->id,
               ]);
            }
            if (isset($db_user->wallet) and $db_user->wallet > 0) {
               \Core\Wallet\Models\WalletTransaction::createQuietly([
                  'type'            => "deposit",
                  'amount'          => $db_user->wallet,
                  'wallet_before'   => 0,
                  'wallet_after'    => $db_user->wallet,
                  'status'          => 'accepted',
                  'user_id'         => $db_user->id
               ]);
            }
         }
         $db_profiles = DB::connection('mysql2')->table('profiles')->get();
         foreach ($db_profiles as $db_profile) {
            try {
               $profile = \Core\Users\Models\Profile::insert([
                  'id' =>  $db_profile->id,
                  'country_id' => $db_profile->country_id,
                  'city_id' => $db_profile->city_id,
                  'district_id' => $districtIds[$db_profile->district_id] ?? null,
                  'other_city_name' => $db_profile->other_city_name,
                  'user_id' => $db_profile->user_id,
                  'bio' => $db_profile->bio,
                  'lat' => $db_profile->lat,
                  'lng' => $db_profile->lng,
                  'created_at' => $db_profile->created_at,
                  'updated_at' => $db_profile->updated_at,
                  'deleted_at' => @$db_profile->deleted_at,

               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }
         //////////////////////////////////////////
         $db_userEditRequests = DB::connection('mysql2')->table('client_edit_profile_requests')->get();
         foreach ($db_userEditRequests as $db_userEditRequest) {
            try {
               $userEditRequest = \Core\Users\Models\UserEditRequest::insert([
                  'id'           => $db_userEditRequest->id,
                  'fullname'     => $db_userEditRequest->fullname,
                  'email'        => @$db_userEditRequest->email,
                  'phone'        => @$db_userEditRequest->phone,
                  'user_id'      => $db_userEditRequest->user_id,
                  'created_at'   => $db_userEditRequest->created_at,
                  'updated_at'   => $db_userEditRequest->updated_at,
                  'deleted_at'   => @$db_userEditRequest->deleted_at,
               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }
     

         //////////////////////////////////////////
         $db_coupons = DB::connection('mysql2')->table('coupons')->get();
         foreach ($db_coupons as $db_coupon) {
            if (\Core\Coupons\Models\Coupon::withTrashed()->whereNotNull('code')->where('code', $db_coupon->code)->exists()) {
               $db_coupon->code = 'nq-' . $db_coupon->id . '-' . $db_coupon->code;
            }

            $coupon = \Core\Coupons\Models\Coupon::insert([
               'id' =>  $db_coupon->id,
               'code' => $db_coupon->code,
               'minimum_price' => $db_coupon->minimum_price,
               'type' => $db_coupon->discount_type,
               'value' => $db_coupon->discount_percentage,
               'status' => "active",
               'applying' => "manual",
               'expired_at' => $db_coupon->expired_date,
               'created_at' => $db_coupon->created_at,
               'updated_at' => $db_coupon->updated_at,
               'deleted_at' => @$db_coupon->deleted_at,
            ]);
         }
         //////////////////////////////////////////

         $db_walletPackages = DB::connection('mysql2')->table('wallet_packages')->get();
         foreach ($db_walletPackages as $db_walletPackage) {

            $walletPackage = \Core\Wallet\Models\WalletPackage::insert([
               'id' =>  $db_walletPackage->id,
               'image' => "oimage",
               'price' => $db_walletPackage->price,
               'value' => $db_walletPackage->value,
               'status' => $db_walletPackage->status,
               'created_at' => $db_walletPackage->created_at,
               'updated_at' => $db_walletPackage->updated_at,
               'deleted_at' => @$db_walletPackage->deleted_at,

            ]);
         }
         //////////////////////////////////////////
         function getCatType($type)
         {
            $categoriesType = [
               'clothes'         => ['clothes', 'clothe', 'fastorder'],
               'sales'           => ['sales', 'sale'],
               'services'        => ['services', 'service'],
               'maid'            => ['maid-host', 'home-maid', 'maidflex', 'maidscheduled', 'maidPackage', 'maidoffer', 'flexible-home-visit', 'scheduled-visits', 'monthly-packages', 'resident-worker-packages'],
               'host'            => ['host-service', 'host', 'care', 'care-host', 'event-hospitality', 'childcare-services', 'massage-services', "personal-hospitality-service", "hospitality-services", "corporate-hospitality-services", "corporate-hospitality-services", "relaxation-massage", "care-service", "selfcare-service", 'selfcare', 'child-care', 'elderly-care'],
            ];
            $categoriesType;
            foreach ($categoriesType as $key => $value) {
               if (in_array($type, $value)) {
                  return $key;
               }
            }
            return "no-type";
         }
         $db_categories = DB::connection('mysql2')->table('categories')->get();
         foreach ($db_categories as $db_category) {
            $trans            =  DB::connection('mysql2')->table('category_translations')->where('locale', 'en')->where('category_id', $db_category->id)->first();

            $db_category->slug =  \Str::slug($trans->name);
            if (\Core\Categories\Models\Category::withTrashed()->orWhere('slug', $db_category->slug)->exists()) {
               $db_category->slug = $trans->name . \Str::random(5);
            }

            $category = \Core\Categories\Models\Category::insert([
               'id' =>  $db_category->id,
               'slug' => $db_category->slug,
               'image' => "thisisimageplace",
               'type' => getCatType($db_category->type),
               'sort' => $db_category->sort,
               'is_package' => $db_category->is_package,
               'status' => $db_category->status == 'active' ? "active" : 'not-active',
               'city_id' => $db_category->city_id,
               'created_at' => $db_category->created_at,
               'updated_at' => $db_category->updated_at,
               'deleted_at' => @$db_category->deleted_at,
            ]);
         }

         $db_categories_translations = DB::connection('mysql2')->table('category_translations')->get();
         foreach ($db_categories_translations as $db_category_translations) {
            try {
               \Core\Categories\Models\CategoryTranslation::insert([
                  'name'         => $db_category_translations->name,
                  'intro'        => $db_category_translations->intro ?? null,
                  'desc'         => $db_category_translations->desc ?? null,
                  'category_id'  => $db_category_translations->category_id,
                  'locale'       => $db_category_translations->locale,

               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }

         $subCategoriesIds = [];
         $db_sub_categories = DB::connection('mysql2')->table('sub_categories')->get();
         foreach ($db_sub_categories as $db_category) {
            $trans            =  DB::connection('mysql2')->table('sub_category_translations')->where('locale', 'en')->where('sub_category_id', $db_category->id)->first();
            $db_category->slug =  \Str::slug($trans->name);
            if (\Core\Categories\Models\Category::withTrashed()->orWhere('slug', $db_category->slug)->exists()) {
               $db_category->slug = $db_category->slug . \Str::random(5);
            }
            $parentCategory = $db_categories->where('id', $db_category->category_id)->first();
            $sub_category = \Core\Categories\Models\Category::createQuietly([
               'slug'            => $db_category->slug,
               'image'           => "no-image",
               'type'            => getCatType($parentCategory->type),
               'sort'            => $parentCategory->sort,
               'is_package'      => $parentCategory->is_package ?? false,
               'parent_id'       => $parentCategory->id ?? null,
               'status'          => $db_category->status == 'active' ? "active" : 'not-active',
               'created_at'      => @$parentCategory->created_at,
               'updated_at'      => @$parentCategory->updated_at,
               'deleted_at'      => @$parentCategory->deleted_at,

            ]);
            $subCategoriesIds[$db_category->id] = $sub_category->id;
         }

         $db_sub_categories_translations = DB::connection('mysql2')->table('sub_category_translations')->get();
         foreach ($db_sub_categories_translations as $db_category_translations) {
            try {
               \Core\Categories\Models\CategoryTranslation::insert([
                  'name' => $db_category_translations->name,
                  'intro' => $db_category_translations->intro  ?? null,
                  'desc' => $db_category_translations->desc ?? null,
                  'category_id' => $subCategoriesIds[$db_category_translations->sub_category_id] ?? null,
                  'locale' => $db_category_translations->locale,

               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }
         $customServicesIds = [];
         $db_custom_services = DB::connection('mysql2')->table('custom_services')->get();
         foreach ($db_custom_services as $db_custom_service) {
            $trans            =  DB::connection('mysql2')->table('custom_service_translations')->where('locale', 'en')->where('custom_service_id', $db_custom_service->id)->first();
            $db_custom_service->slug =  \Str::slug($trans->name);
            if (\Core\Categories\Models\Category::withTrashed()->orWhere('slug', $db_custom_service->slug)->exists()) {
               $db_custom_service->slug = $trans->name . \Str::random(5);
            }
            $parentCategory = isset($db_custom_service->parent_id) ? $db_categories->where('id', $customServicesIds[$db_custom_service->parent_id])->first() : null;
            $custom_service = \Core\Categories\Models\Category::createQuietly([
               'slug'            => $db_custom_service->slug,
               'image'           => "no-image",
               'type'            => getCatType($db_custom_service->slug),
               'parent_id'       => $customServicesIds[$db_custom_service->parent_id] ?? null,
               'sort'            => $parentCategory->sort ?? 5,
               'is_package'      => $parentCategory->is_package ?? false,
               'status'          => $db_custom_service->status == 'active' ? "active" : 'not-active',
               'created_at'      => @$parentCategory->created_at,
               'updated_at'      => @$parentCategory->updated_at,
               'deleted_at'      => @$parentCategory->deleted_at,

            ]);
            $customServicesIds[$db_custom_service->id] = $custom_service->id;
         }

         $db_custom_services_translations = DB::connection('mysql2')->table('custom_service_translations')->get();
         foreach ($db_custom_services_translations as $db_custom_service_translations) {
            try {
               \Core\Categories\Models\CategoryTranslation::insert([
                  'name' => $db_custom_service_translations->name,
                  'intro' => $db_custom_service_translations->intro ?? null,
                  'desc' => $db_custom_service_translations->desc ?? null,
                  'category_id' => $customServicesIds[$db_custom_service_translations->custom_service_id],
                  'locale' => $db_custom_service_translations->locale,

               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }

         //////////////////////////////////////////

         $db_categoryTypes = DB::connection('mysql2')->table('service_types')->get();
         foreach ($db_categoryTypes as $db_categoryType) {

            if (\Core\Categories\Models\CategoryType::withTrashed()->orWhere('slug', $db_categoryType->slug)->exists()) {
               $db_categoryType->slug .= \Str::uuid(5);
            }

            $categoryType = \Core\Categories\Models\CategoryType::insert([
               'id'          =>  $db_categoryType->id,
               'slug'        => $db_categoryType->slug,
               'category_id' => $customServicesIds[$db_categoryType->custom_service_id],
               'hour_price'  => $db_categoryType->hour_price ?? 0,
               'status'      => $db_categoryType->status == 'active' ? "active" : "not-active",
               'created_at' => $db_categoryType->created_at,
               'updated_at' => $db_categoryType->updated_at,
               'deleted_at' => @$db_categoryType->deleted_at,
            ]);
         }

         $db_categoryTypes_translations = DB::connection('mysql2')->table('service_type_translations')->get();
         foreach ($db_categoryTypes_translations as $db_categoryType_translations) {
            try {
               \Core\Categories\Models\CategoryTypeTranslation::insert([
                  'name' => $db_categoryType_translations->name,
                  'intro' => $db_categoryType_translations->intro,
                  'desc' => $db_categoryType_translations->desc,
                  'category_type_id' => $db_categoryType_translations->service_type_id,
                  'locale' => $db_categoryType_translations->locale,

               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }

         $db_categorySettings = DB::connection('mysql2')->table('service_settings')->get();
         foreach ($db_categorySettings as $db_categorySetting) {
            $trans            =  DB::connection('mysql2')->table('service_setting_translations')->where('locale', 'en')->where('service_setting_id', $db_categorySetting->id)->first();
            if ($trans) {
               $db_categorySetting->slug =  \Str::slug($trans->name);
            }
            if (!isset($db_categorySetting->slug)) {
               $db_categorySetting->slug = \Str::random(5);
            }
            if (\Core\Categories\Models\CategorySetting::withTrashed()->where('slug', $db_categorySetting->slug)->exists()) {
               $db_categorySetting->slug =  \Str::slug($trans->name) .  \Str::random(5);
            }
            try {
               //code...
               $categorySetting = \Core\Categories\Models\CategorySetting::insert([
                  'id'           => $db_categorySetting->id,
                  'slug'         => $db_categorySetting->slug,
                  'category_id'  => $customServicesIds[$db_categorySetting->custom_service_id],
                  'addon_price'  => $db_categorySetting->addon_price ?? 0,
                  'cost'         => $db_categorySetting->addon_price  * 0.7,
                  'status'       => $db_categorySetting->status,
                  'created_at'   => $db_categorySetting->created_at,
                  'updated_at'   => $db_categorySetting->updated_at,
                  'deleted_at'   => @$db_categorySetting->deleted_at,
               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }
         foreach ($db_categorySettings as $db_categorySetting) {
           DB::table('category_settings')->where('id',$db_categorySetting->id)
            ->update([
               'parent_id' => $db_categorySetting->parent_id,
            ]);
            # code...
         }

         $db_categorySettings_translations = DB::connection('mysql2')->table('service_setting_translations')->get();
         foreach ($db_categorySettings_translations as $db_categorySetting_translations) {
            try {
               \Core\Categories\Models\CategorySettingTranslation::insert([
                  'name'                  => $db_categorySetting_translations->name,
                  'category_setting_id'   => $db_categorySetting_translations->service_setting_id,
                  'locale'                => $db_categorySetting_translations->locale,

               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }
         $db_home_maid_sales = DB::connection('mysql2')->table('home_maid_sales')->get();
         foreach ($db_home_maid_sales as $home_maid_sale) {
            try {
               //code...
               $categorySetting = \Core\Categories\Models\CategoryOffer::insert([
                  'id'           => $home_maid_sale->id,
                  'price'        => $home_maid_sale->price,
                  'sale_price'   => $home_maid_sale->sale_price,
                  'hours_num'    => $home_maid_sale->hours_num ?? 0,
                  'workers_num'  => $home_maid_sale->workers_num ?? 0,
                  'type'         => $home_maid_sale->type,
                  'status'       => 'active',
                  'created_at' => $home_maid_sale->created_at,
                  'updated_at' => $home_maid_sale->updated_at,
                  'deleted_at' => @$home_maid_sale->deleted_at,
               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }

         $db_home_maid_sale_translations = DB::connection('mysql2')->table('home_maid_sale_translations')->get();
         // dd($db_home_maid_sale_translations);
         foreach ($db_home_maid_sale_translations as $db_home_maid_sale_translation) {
            try {
               \Core\Categories\Models\CategoryOfferTranslation::insert([
                  'name'                  => $db_home_maid_sale_translation->name,
                  'desc'                  => $db_home_maid_sale_translation->desc,
                  'category_offer_id'     => $db_home_maid_sale_translation->home_maid_sale_id,
                  'locale'                => $db_home_maid_sale_translation->locale,

               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }

         //////////////////////////////////////////

         $db_sliders = DB::connection('mysql2')->table('sliders')->get();
         foreach ($db_sliders as $db_slider) {
            try {
               //code...
               $slider = \Core\Categories\Models\Slider::insert([
                  'id'           =>  $db_slider->id,
                  'category_id'  => $db_slider->category_id ?? $customServicesIds[$db_slider->service_id] ?? null,
                  'type'         => getCatType($db_slider->type),
                  'status'       => $db_slider->status,
                  'city_id'      => $db_slider->city_id,
                  'created_at'   => @$db_slider->created_at,
                  'updated_at'   => @$db_slider->updated_at,
                  'deleted_at'   => @$db_slider->deleted_at,

               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th, $db_slider);
            }
         }

         //////////////////////////////////////////

         $db_products = DB::connection('mysql2')->table('products')->get();
         foreach ($db_products as $db_product) {
            if (\Core\Products\Models\Product::withTrashed()->whereNotNull('sku')->where('sku', $db_product->sku)->exists()) {
               $db_product->sku = "nq-" . $db_product->id . '-' . $db_product->sku;
            }

            $product = \Core\Products\Models\Product::insert([
               'id' =>  $db_product->id,
               'image' => "image",
               'type' => getCatType($db_product->type),
               'sku' => $db_product->sku,
               'is_package' => $db_product->is_package,
               'category_id' => $db_product->category_id,
               'sub_category_id' => $db_product->sub_category_id ? $subCategoriesIds[$db_product->sub_category_id] : null,
               'price' => $db_product->price,
               'cost' => $db_product->price * 0.7,
               'quantity' => $db_product->quantity,
               'status' => "active",
               'created_at' => $db_product->created_at,
               'updated_at' => $db_product->updated_at,
               'deleted_at' => @$db_product->deleted_at,
            ]);
         }

         $db_products_translations = DB::connection('mysql2')->table('product_translations')->get();
         foreach ($db_products_translations as $db_product_translations) {
            try {
               \Core\Products\Models\ProductTranslation::insert([
                  'name' => $db_product_translations->name,
                  'desc' => $db_product_translations->desc,
                  'product_id' => $db_product_translations->product_id,
                  'locale' => $db_product_translations->locale,

               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }
         //////////////////////////////////////////

         $db_reportReasons = DB::connection('mysql2')->table('report_reasons')->get();
         foreach ($db_reportReasons as $db_reportReason) {

            $reportReason = \Core\Orders\Models\ReportReason::insert([
               'id' =>  $db_reportReason->id,
               'ordering' => $db_reportReason->ordering,
               'created_at' => $db_reportReason->created_at,
               'updated_at' => $db_reportReason->updated_at,
               'deleted_at' => @$db_reportReason->deleted_at,
            ]);
         }

         $db_reportReasons_translations = DB::connection('mysql2')->table('report_reason_translations')->get();
         foreach ($db_reportReasons_translations as $db_reportReason_translations) {
            try {
               \Core\Orders\Models\ReportReasonTranslation::insert([
                  'name' => $db_reportReason_translations->name,
                  'desc' => $db_reportReason_translations->desc ?? null,
                  'report_reason_id' => $db_reportReason_translations->report_reason_id,
                  'locale' => $db_reportReason_translations->locale,

               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }
         //////////////////////////////////////////
         $db_carts = DB::connection('mysql2')->table('carts')->get();
         foreach ($db_carts as $db_cart) {
            try {
               \Core\Orders\Models\Cart::insert([
                  'user_id'      => $db_cart->user_id,
                  'phone'        => $db_cart->phone,
                  'data'         => $db_cart->data,
                  'created_at'   => $db_cart->created_at,
                  'updated_at'   => $db_cart->updated_at,
                  'deleted_at'   => @$db_cart->deleted_at,
               ]);
            } catch (\Throwable $th) {
               //throw $th;
               $owner =  DB::connection('mysql2')->table('users')->where('id', $db_cart->user_id)->first();
               if (isset($owner)) {
                  dd($th, $db_cart, $owner);
               }
            }
         }
         //////////////////////////////////////////


         $db_orders = DB::connection('mysql2')->table('orders')->get();
         foreach ($db_orders as $db_order) {
            if (\Core\Orders\Models\Order::withTrashed()->whereNotNull('reference_id')->where('reference_id', $db_order->reference_id)->exists()) {
               $db_order->reference_id = 'nq-' . $db_order->id . '-' . $db_order->reference_id;
            }
            $cityId     = $db_profiles->where('user_id', $db_order->client_id)->first()?->city_id  ?? null;
            $distinctId = $db_profiles->where('user_id', $db_order->client_id)->first()?->district_id ?? null;

            try {
               if (isset($db_order->service_days_per_week) and !empty($db_order->service_days_per_week)) {
                  $days = explode(',', $db_order->service_days_per_week);
                  foreach ($days as $key => $day) {
                     $days[$key] = \Str::lower($day);
                  }
                  $db_order->service_days_per_week = json_encode($days);
               }
               if (isset($db_order->service_days_per_month) and !empty($db_order->service_days_per_month)) {
                  $days = explode(',', $db_order->service_days_per_month);
                  foreach ($days as $key => $day) {
                     $day        = explode('-', $day);
                     $day        = "{$day[1]}-{$day[2]}-{$day[3]}";;
                     $days[$key] = \Str::lower($day);
                  }
                  $db_order->service_days_per_month = json_encode($days);
               }else{
                  $db_order->service_days_per_month = null;
               }
               
               //code...
               $order = \Core\Orders\Models\Order::insert([
                  'id'                    => $db_order->id,
                  'reference_id'          => $db_order->reference_id,
                  'type'                  => $db_order->type,
                  'status'                => $db_order->status,
                  'client_id'             => $db_order->client_id,
                  'pay_type'              => $db_order->pay_type,
                  'transaction_id'        => $db_order->transaction_id,
                  'order_status_times'    => $db_order->order_status_times,
                  'days_per_week'         => $db_order->days_per_week,
                  'days_per_week_names'   => $db_order->service_days_per_week,
                  'days_per_month_dates'  => $db_order->service_days_per_month,
                  'coupon_id'             => $db_order->coupon_id,
                  'coupon_data'           => $db_order->coupon_data,
                  'order_price'           => $db_order->order_price,
                  'total_cost'            => $db_order->order_price * 0.7,
                  'total_coupon'          => ($db_order->order_price * ($db_order->coupon_discount_percentage /100)),
                  'delivery_price'        => $db_order->delivery_price,
                  'total_price'           => $db_order->total_price,
                  'is_admin_accepted'     => $db_order->is_admin_accepted,
                  'admin_cancel_reason'   => $db_order->admin_cancel_reason,
                  'wallet_used'           => (ToolHelper::getBooleanValue($db_order->wallet_used)) ? 1 : 0,
                  'wallet_amount_used'    => $db_order->wallet_balance - $db_order->total_after_wallet,
                  'city_id'               => $cityId,
                  'district_id'           => $districtIds[$distinctId] ?? null,
                  'created_at'            => $db_order->created_at,
                  'updated_at'            => $db_order->updated_at,
                  'deleted_at'            => @$db_order->deleted_at,
               ]);
               if (isset($db_order->receiving_date)) {
                  try {
                     \Core\Orders\Models\OrderRepresentative::createQuietly([
                        'order_id'           => $db_order->id,
                        'representative_id'  => $db_order->receiving_driver_id,
                        'type'               => 'receiver',
                        'date'               => $db_order->receiving_date,
                        'time'               => $db_order->receiving_time,
                        'to_time'            => $db_order->receiving_to_time,
                        'lat'                => $db_order->receiving_lat,
                        'lng'                => $db_order->receiving_lng,
                        'location'           => $db_order->receiving_location,
                        'for_all_items'      => true,
                     ]);
                     //code...
                  } catch (\Throwable $th) {
                     //throw $th;
                     dd($th);
                  }
               }
               if (isset($db_order->delivery_date)) {
                  try {
                     //code...
                     \Core\Orders\Models\OrderRepresentative::createQuietly([
                        'order_id'           => $db_order->id,
                        'representative_id'  => $db_order->delivery_driver_id,
                        'type'               => 'delivery',
                        'date'               => $db_order->delivery_date,
                        'time'               => $db_order->delivery_time,
                        'to_time'            => $db_order->delivery_to_time,
                        'lat'                => $db_order->delivery_lat,
                        'lng'                => $db_order->delivery_lng,
                        'location'           => $db_order->delivery_location,
                        'for_all_items'      => true,
                     ]);
                  } catch (\Throwable $th) {
                     //throw $th;
                     dd($th);
                  }
               }
               if (isset($db_order->execute_date)) {
                  try {
                     //code...
                     \Core\Orders\Models\OrderRepresentative::createQuietly([
                        'order_id'           => $db_order->id,
                        'representative_id'  => $db_order->technical_id,
                        'type'               => 'delivery',
                        'date'               => $db_order->execute_date,
                        'time'               => $db_order->execute_time,
                        'to_time'            => $db_order->execute_to_time,
                        'lat'                => $db_order->receiving_lat,
                        'lng'                => $db_order->receiving_lng,
                        'location'           => $db_order->receiving_location,
                        'for_all_items'      => true,

                     ]);
                  } catch (\Throwable $th) {
                     //throw $th;
                     dd($th);
                  }
               }
               if (isset($db_order->service_data)) {
                  try {
                     \Core\Orders\Models\OrderTypesOfDatum::createQuietly([
                        'order_id'  => $db_order->id,
                        'key'       => "service_data",
                        'value'     => $db_order->service_data,

                     ]);
                     //code...
                  } catch (\Throwable $th) {
                     //throw $th;
                     dd($th);
                  }
               }
               if (isset($db_order->service_type_data)) {
                  try {
                     //code...
                     \Core\Orders\Models\OrderTypesOfDatum::createQuietly([
                        'order_id'  => $db_order->id,
                        'key'       => "service_type_data",
                        'value'     => $db_order->service_type_data,

                     ]);
                  } catch (\Throwable $th) {
                     //throw $th;
                     dd($th);
                  }
               }
               if (isset($db_order->uniform_data)) {
                  try {
                     //code...
                     \Core\Orders\Models\OrderTypesOfDatum::createQuietly([
                        'order_id'  => $db_order->id,
                        'key'       => "uniform_data",
                        'value'     => $db_order->uniform_data,

                     ]);
                  } catch (\Throwable $th) {
                     //throw $th;
                     dd($th);
                  }
               }
               if (isset($db_order->worker_count_data)) {
                  try {
                     //code...
                     \Core\Orders\Models\OrderTypesOfDatum::createQuietly([
                        'order_id'  => $db_order->id,
                        'key'       => "worker_count_data",
                        'value'     => $db_order->worker_count_data,

                     ]);
                  } catch (\Throwable $th) {
                     //throw $th;
                     dd($th);
                  }
               }
               if (isset($db_order->hours_count_data)) {
                  try {
                     //code...
                     \Core\Orders\Models\OrderTypesOfDatum::createQuietly([
                        'order_id'  => $db_order->id,
                        'key'       => "hours_count_data",
                        'value'     => $db_order->hours_count_data,

                     ]);
                  } catch (\Throwable $th) {
                     //throw $th;
                     dd($th);
                  }
               }
               if (isset($db_order->period_data)) {
                  try {
                     //code...
                     \Core\Orders\Models\OrderTypesOfDatum::createQuietly([
                        'order_id'  => $db_order->id,
                        'key'       => "period_data",
                        'value'     => $db_order->period_data,

                     ]);
                  } catch (\Throwable $th) {
                     //throw $th;
                     dd($th);
                  }
               }
               if (isset($db_order->duration_data)) {
                  try {
                     //code...
                     \Core\Orders\Models\OrderTypesOfDatum::createQuietly([
                        'order_id'  => $db_order->id,
                        'key'       => "duration_data",
                        'value'     => $db_order->duration_data,

                     ]);
                  } catch (\Throwable $th) {
                     //throw $th;
                     dd($th);
                  }
               }
               if (isset($db_order->additional_data)) {
                  try {
                     //code...
                     \Core\Orders\Models\OrderTypesOfDatum::createQuietly([
                        'order_id'  => $db_order->id,
                        'key'       => "additional_data",
                        'value'     => $db_order->additional_data,

                     ]);
                  } catch (\Throwable $th) {
                     //throw $th;
                     dd($th);
                  }
               }
            } catch (\Throwable $th) {
               //throw $th;
               dd($th, $db_order);
            }
         }
         $db_orderItems = DB::connection('mysql2')->table('order_items')->get();
         foreach ($db_orderItems as $db_orderItem) {
            try {
               //code..
               $productId     = \Core\Products\Models\Product::find($db_orderItem->product_id)?->id;
               $orderId       = \Core\Orders\Models\Order::find($db_orderItem->order_id)?->id;
               $productData   = $db_orderItem->product_data;
               if (isset($productData) and !empty($productData)) {
                  $productData                     = json_decode($productData, true);
                  $productData['sub_category_id']  = $subCategoriesIds[$productData['sub_category_id']] ?? null;
                  $productData                     = json_encode($productData);
               }
               $orderItem     = \Core\Orders\Models\OrderItem::insert([
                  'id'              => $db_orderItem->id,
                  'order_id'        => $orderId,
                  'product_id'      => $productId,
                  'product_name'    => "",
                  'product_data'    => $productData,
                  'product_price'   => $db_orderItem->product_price,
                  'quantity'        => $db_orderItem->quantity,
                  'height'          => isset($db_orderItem->carpet_size) ? $db_orderItem->carpet_size : 0,
                  'width'           => isset($db_orderItem->carpet_size) ? 1 : 0,
                  'add_by_admin'    => $db_orderItem->add_by_admin,
                  'update_by_admin' => $db_orderItem->update_by_admin,
                  'is_delivered'    => true,
                  'is_picked'       => true,
                  'created_at'      => $db_orderItem->created_at,
                  'updated_at'      => $db_orderItem->updated_at,
                  'deleted_at'      => @$db_orderItem->deleted_at,


               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }

         $db_orderItemQtyUpdates = DB::connection('mysql2')->table('order_item_updates')->get();
         foreach ($db_orderItemQtyUpdates as $db_orderItemQtyUpdate) {
            try {
               //code...
               $orderItemQtyUpdate = \Core\Orders\Models\OrderItemQtyUpdate::insert([
                  'id'              =>  $db_orderItemQtyUpdate->id,
                  'item_id'         => $db_orderItemQtyUpdate->item_id,
                  'from'            => $db_orderItemQtyUpdate->from,
                  'to'              => $db_orderItemQtyUpdate->to,
                  'updater_email'   => $db_orderItemQtyUpdate->updater_email,
               ]);
            } catch (\Throwable $th) {
               //throw $th;
               dd($th);
            }
         }

         //////////////////////////////////////////
         $medias = DB::connection('mysql2')->table('app_media')->get();
         foreach ($medias as $media) {
            $newMedia = Media::updateOrCreate([
               'file_name' => "images/" . $media->media
            ], [
               'file_type' => 'image',
               'size'      => 0,
               'alt'       => $media->media,
               'title'     => $media->media,
            ]);
            if ($media->app_mediaable_type == 'App\Models\Category') {
               \Core\Categories\Models\Category::where('id', $media->app_mediaable_id)->update([
                  'image' => $newMedia->file_name
               ]);
            } else if ($media->app_mediaable_type == 'App\Models\City') {
               \Core\Info\Models\City::where('id', $media->app_mediaable_id)->update([
                  'image' => $newMedia->file_name
               ]);
            } else if ($media->app_mediaable_type == 'App\Models\CustomService') {
               \Core\Categories\Models\Category::where('id', $customServicesIds[$media->app_mediaable_id] ?? "null")->update([
                  'image' => $newMedia->file_name
               ]);
            } else if ($media->app_mediaable_type == 'App\Models\Product') {
               \Core\Products\Models\Product::where('id', $media->app_mediaable_id)->update([
                  'image'        => $newMedia->file_name,
               ]);
            } else if ($media->app_mediaable_type == 'App\Models\Slider') {
               $lang =  ($media->option == 'slider_en') ? "en" :"ar";
               \Core\Categories\Models\Slider::where('id', $media->app_mediaable_id)->update([
                  'image_'. $lang => $newMedia->file_name
               ]);
            } else if ($media->app_mediaable_type == 'App\Models\WalletPackage') {
               \Core\Wallet\Models\WalletPackage::where('id', $media->app_mediaable_id)->update([
                  'image' => $newMedia->file_name
               ]);
            }
         }
         $news_letters = DB::connection('mysql2')->table('news_letters')->get();
         foreach ($news_letters as $key => $news_letter) {
            try {
               //code...
               DB::table('news_letters')->insert([
                  'email' => $news_letter->email ?? null
               ]);
            } catch (\Throwable $th) {
               //throw $th;
            }
         }
         $settings = DB::connection('mysql2')->table('settings')->get();
         foreach ($settings as $key => $setting) {
            try {
               //code...
               DB::table('settings')->insert([
                  'key' => $setting->key,
                  'value' => $setting->value 
               ]);
            } catch (\Throwable $th) {
               //throw $th;
            }
         }
      });
   }
}

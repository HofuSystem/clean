<?php

namespace Core\Info\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Info\DataResources\Api\DistrictResource;
use Core\Info\DataResources\Api\CityResource;
use Core\Info\Models\City;
use Core\Info\Models\District;
use Core\Settings\Traits\ApiResponse;
use Core\Info\Services\CitiesService;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Cache;

/**
 * @group 4. General & Settings
 * @subgroup Geography
 */
class CitiesController extends Controller
{
    use ApiResponse;
    public function __construct(protected CitiesService $citiesService){}

    public function list(Request $request)
    {
        $locale = app()->getLocale();
        $cities = Cache::remember("api_cities_active_{$locale}", 3600, function () {
            return City::with('translations')->where('status', 'active')->get();
        });
        return $this->returnData(trans('cities'), ['data' => CityResource::collection($cities)]);
    }

    public function districts(Request $request, $id)
    {
        $locale = app()->getLocale();
        $districts = Cache::remember("api_districts_city_{$id}_{$locale}", 3600, function () use ($id) {
            return District::with(['translations', 'mapPoints'])->where('city_id', $id)->get();
        });
        return $this->returnData(trans('districts'), ['data' => DistrictResource::collection($districts)]);
    }


  
    
}

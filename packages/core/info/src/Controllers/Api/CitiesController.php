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


class CitiesController extends Controller
{
    use ApiResponse;
    public function __construct(protected CitiesService $citiesService){}

    public function list(Request $request){
     $cities = City::with('translations')->where('status','active')->get();
     return $this->returnData(trans('cities'),['data' => CityResource::collection($cities)]);
    }
    public function districts(Request $request,$id){
     $districts = District::with(['translations','mapPoints'])->where('city_id',$id)->get();
     return $this->returnData(trans('districts'),['data' => DistrictResource::collection($districts)]);
    }


  
    
}

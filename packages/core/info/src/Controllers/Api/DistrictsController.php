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


class DistrictsController extends Controller
{
    use ApiResponse;
    public function __construct(){}

    public function list(Request $request){
     $districts = District::with(['translations','mapPoints'])->get();
     return $this->returnData(trans('districts'),['data' => DistrictResource::collection($districts)]);
    }
 


  
    
}

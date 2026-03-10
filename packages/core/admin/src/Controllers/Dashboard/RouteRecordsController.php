<?php

namespace Core\Admin\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use Core\Admin\Services\RouteRecordsService;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;

class RouteRecordsController extends Controller
{
    function __construct(protected RouteRecordsService $routeRecordsService){}
    use ApiResponse;
    
    public function index(Request $request)
    {
        $data             = $this->routeRecordsService->getRoutesAnalysis($request->time_filter); 
        $data['title']    = trans('Routes Analysis');
        $data['screen']   = 'routes-analysis';
        return view('admin::pages.routes-analysis.index',$data);
    }

}

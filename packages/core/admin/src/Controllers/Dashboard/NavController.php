<?php

namespace Core\Admin\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Admin\Services\DashboardService;
use Core\Settings\Helpers\ToolHelper;
use Illuminate\Http\Request;

use Core\Users\Models\Permission;

class NavController extends Controller
{
    public function index(Request $request,$slug){
        $GetRoutes = \Illuminate\Support\Facades\Route::getRoutes()->getRoutesByMethod()['GET'];
        $routes    = [];
        foreach ($GetRoutes as $key => $route) {
            if(str_starts_with($route->getName(),'api')){
                continue;
            }
            preg_match_all('/\{(.*?)\}/', $key, $matches);
            $matches = $matches[1];
            foreach ($matches as $key => $value) {
                if(str_ends_with($value,'?')){
                    $matches[$key] = substr($value, 0, -1);
                }
            }
            $routes[$route->getName()] = $matches;
        }
        $data['navBar']         = DashboardService::getMenuRaw($slug);
        $data['navBar']         = json_decode(json_encode($data['navBar']));
        $data['screen']         = 'dashboard-home';
        $data['title']          = trans('nav bar');
        $data['permissions']    = Permission::select('name')->get()->pluck('name')->toArray();
        $data['routes']         = $routes;
        $data['slug']           = $slug;
        return view('admin::pages.nav-bar.index',$data);
    }
    public function save(Request $request,$slug)
    { 
        $json  = json_encode($request->nav);
        if(!file_exists(base_path("packages/core/admin/src/nav"))){
            mkdir(base_path("packages/core/admin/src/nav"),0777);
        }
        file_put_contents(base_path("packages/core/admin/src/nav/$slug.json"),$json);
        return response()->json(['message' => trans("Nav saved")]);
    }
  
}

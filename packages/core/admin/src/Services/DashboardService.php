<?php 
namespace Core\Admin\Services;
class DashboardService{
    static function getMenuRaw($slug ='admin-nav')  {
      
        if(file_exists(base_path("packages/core/admin/src/nav/$slug.json"))){
            $nav =   json_decode(file_get_contents(base_path("packages/core/admin/src/nav/$slug.json")),1);
        }else{
            $nav = [];
        }
        return $nav;

    }
    static function getMenu($slug ='admin-nav')  {
        $nav =   self::getMenuRaw($slug);
        return self::formatItems($nav);
    }
    static function formatItems($items)  {
        foreach ($items as $index => $item) {
            $items[$index] = self::formatItem($item);
        }
        return $items;
    }
    static function formatItem($item)  {
        try {
            $item['active'] = false;
            if(isset($item['route']) and !empty($item['route'])){
                if($item['route'] == 'dashboard.cms-pages.index'){
                    // dd($item['route'],$item['routeArray'], route($item['route'],$item['routeArray'] ?? []));
                }
                $item['url'] = route($item['route'],$item['routeArray'] ?? []);
            }
            if ($item['url'] == request()->url()) {
                $item['active'] = true;
            }
            if(isset($item['sub']) and !empty($item['sub'])){
                $item['sub'] = self::formatItems($item['sub']);
                $active      = false;
                foreach ($item['sub'] as $sub) {
                    if($sub['active']){
                        $active = true;
                    }
                }
                if($active){
                    $item['active'] = true;
                }
            }
            $item['titleLocale'] = $item['title'][config('app.locale')] ?? "";
        } catch (\Throwable $th) {
        }

        return $item;
    }
}
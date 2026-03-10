<?php 
namespace Core\Admin\Helpers;

use Core\MediaCenter\Helpers\MediaCenterHelper;


class DashboardDataTableFormatter{
    public static function relations($list,$key,$route){
        if ($list instanceof \Illuminate\Database\Eloquent\Collection) {
            $listHtml = "[";
            foreach ($list as $index => $single) {
                if($index != 0){
                    $listHtml .= ",";
                }
                $listHtml .= self::relation($single,$key,$route);
    
            }
            $listHtml .= "]";
            return $listHtml;
           
        }else{
            return self::relation($list,$key,$route);
        }
    }
    public static function relation($single,$key,$route){
        
        return isset($single) ?  '<a href="'.route($route,$single->id).'">'.$single->$key.'</a>' : "";
    }
    public static function mediaCenter($values){
        
        if(!isset($values)){
            return null;
        }
        $images     = '<div class="row\">';
        $images     .= MediaCenterHelper::getImagesHtml($values); 
        $images     .= '</div>';
        return $images;
    }
    public static function text($value){
       // Remove HTML tags
        $value = strip_tags($value);
        // Truncate the string to 50 characters
        if (mb_strlen($value, 'UTF-8') > 50) {
            $value = mb_substr($value, 0, 50, 'UTF-8') . '..';
        }
        return $value;
    }
    public static function checkbox($value){
        $type   = $value ? 'success'            : 'danger';
        $status = $value ? trans('yes')   : trans('no');
        return "<div class=\"p-1 alert alert-{$type}\" role=\"alert\">{$status}</div>";
    }
    
}
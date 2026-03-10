<?php 
namespace Core\Admin\Helpers;

use Core\Settings\Services\LanguageService;
use RM\General\Helpers\ToolHelper;

class TransHelper{
    static function add($key)  {
      $data         = self::getDataOfKey('temp');
      $data[$key]   = '';
      self::setDataOfKey('temp',$data);
      foreach (LanguageService::getActiveLangs() as $prefix => $value) {
          $data =  self::getDataOfKey($prefix);
            if(!isset($data[$key])){
                $data[$key] = $key;
                self::setDataOfKey($prefix,$data);
            }
        }
    }
    static function getDataOfKey($key)  {
        return  file_exists(base_path('lang/' . $key . '.json')) ? json_decode(file_get_contents(base_path('lang/' . $key . '.json')), true) : [];
    }
    static function setDataOfKey($key,$data)  {
        file_put_contents(base_path('lang/' . $key . '.json'), json_encode($data,JSON_PRETTY_PRINT));
    }
   
    
}
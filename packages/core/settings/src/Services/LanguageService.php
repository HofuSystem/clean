<?php
namespace Core\Settings\Services;

use Core\Cache\Services\CacheMangerService;
use Core\Logs\Helpers\LogHelper;
use Exception;
use Illuminate\Support\Facades\DB;
use PDO;

class LanguageService{

    public static $activeLangs = null;
      public static function getCurrentLocaleNative(){
        $locale     = config('app.locale');
        $localData = self::getActiveLangs()[$locale]['native'] ?? "English";
        return $localData;
    }
    public static function getCurrentLocaleDir(){
        $locale     = config('app.locale');
        $localData  = self::getActiveLangs()[$locale]['dir'] ?? "ltr";
        return $localData;
    }
    public static function getActiveLangs()
    {
        if(self::$activeLangs){
            return self::$activeLangs;
        }

        try {
            $results    = DB::table('languages')
                ->select(['name','script','native','dir','regional','prefix','flag'])
                ->where('active',1)->get()
                ->keyBy('prefix')
                ->map(function($item){return (array) $item;})
                ->toArray();
            if(isset($results) and empty($results)){
                $results = [
                    'en'=> ['name' => 'English','script' => 'Latn', 'native' => 'English','prefix' => 'en', 'regional' => 'en_GB', "flag" => "us"],
                    'ar'=> ['name' => 'Arabic','script' => 'Arab', 'native' => 'العربية','prefix' => 'ar', 'regional' => 'ar_SA', "flag" => "sa"]
                ];
            }
            //code...
        } catch (\Throwable $th) {
            $results = [
                'en'=> ['name' => 'English','script' => 'Latn', 'native' => 'English','prefix' => 'en', 'regional' => 'en_GB', "flag" => "us"],
                'ar'=> ['name' => 'Arabic','script' => 'Arab', 'native' => 'العربية','prefix' => 'ar', 'regional' => 'ar_SA', "flag" => "sa"]
            ];
            //throw $th;
        }
        self::$activeLangs = $results;
        return $results;
    } // end of active_langs
    
    public static function getDefaultLocale()
    {
        $defaultLang = CacheMangerService::connection('settings')->get('instance-default-lang');

        if(isset($defaultLang)){
            return $defaultLang;
        }

       
        return $defaultLang ?? 'en';
        
    } // end of default_locale
    
}

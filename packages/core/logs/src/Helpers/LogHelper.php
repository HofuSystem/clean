<?php

namespace Core\Logs\Helpers;

use Core\Logs\Models\Log;
use Core\Settings\Helpers\ToolHelper;

class LogHelper {

   public static function get(int $id)
   {
       return Log::find($id);
   }

   public static function register(string $category,string $saveType,$attachedData,string | null $folder = "",bool $main = false)
   {
        if(empty($folder)) {
            $folder = config("backend-settings.logs.logs_directory.default");
        }
        if ($category   ==  'path') {
            return self::registerInPath($saveType,$attachedData,$folder,$main);
        } elseif ($category ==  'database') {
            return self::registerInDB($saveType,$attachedData,$main);
        } else {
            return false;
        }
   }

   public static function registerInPath(string $saveType,$attachedData,string | null $folder,bool $main = false)
   {
        try{
            if(!is_string($attachedData)) {
                $attachedData = json_encode($attachedData);
            }
            $message    = '['.date("Y-m-d-h:i:s").'] ';
            $user       = ToolHelper::getUser();
            if($user) {
                $message .= '{'.$user->name.':'.$user->id.'}';
            }
            $message    .= ' ['.strtoupper($saveType).'] ';
            $fileName   = $saveType.'.log';

            $path       = base_path("storage/log-system/$folder");
            $path       = $path . '/'.date('Y').'/'.date('m').'/'.date('d');
            self::pathCheckAndCreate($path);
            $path       = $path.'/'.$fileName;
            $myFile     = fopen($path, "a");
            chmod($path,0777);
            fwrite($myFile, "\n\n".$message.$attachedData);
            fclose($myFile);
        } catch(\Exception $e) {

        }
   }

   public static function registerInDB(string $saveType,$attachedData,bool $main = false)
   {
        $attachedData = (is_array($attachedData)) ? $attachedData : json_decode($attachedData);
        $attachedData = json_encode($attachedData);
        if($main){
            \DB::connection('main_mysql')->table('logs_sys')->insert([
                'data'      => $attachedData,
                'type'      => $saveType,
                'user_id'   => (auth()->user()) ? auth()->user()->id : null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }else{
            Log::create([
                'data'      => $attachedData,
                'type'      => $saveType,
                'user_id'   => (auth()->user()) ? auth()->user()->id : null
            ]);
        }
   }

   public static function pathCheckAndCreate($file_dir)
   {
       if (!file_exists($file_dir)) {
           mkdir($file_dir, 0777, true);
      }
   }
}

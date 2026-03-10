<?php 
namespace Core\Cache\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Core\General\Helpers\ToolHelper;

class RedisCacheManger implements Cacheable
{
    protected $connection   = "redis";
    protected $path ;
    function __construct(){
        $this->path = config('database.redis.options.prefix').''.config('cache.prefix').":"; 
    }
    public static function connection($connection) : self
    {
        $object             = (new self);
        $object->connection = $connection;
        return $object;
    }
    public function setConnection($connection): static
    {
       $this->connection = $connection;
       return $this;
    }

    function get($key)
    {

        return Cache::driver($this->connection)->get($key);
    }

    function getKeys($term) {

        $keys =  collect(Redis::connection($this->connection)->keys($term))->map(function($key){return str_replace($this->path,"",$key);})->toArray();
        return $keys;
    }

    //set cached data
    public function set($key,$data,$time = null){
        if(isset($time)){
            $this->remember($key,$data,$time);
        }else{
            $this->rememberForever($key,$data);
        }
    }

    function rememberForever($key, $data)
    {
        $this->flush($key);

        return Cache::driver($this->connection)->rememberForever($key, self::dataFunc($data));
    }

    function remember($key, $data, $time)
    {  
        $this->flush($key);

        return Cache::driver($this->connection)->remember($key, $time, self::dataFunc($data));
    }


    function dataFunc($data)
    {
        if (is_callable($data)) {
            return $data;
        } else {

            return function () use ($data) {
                return $data;
            };
        }
    }

    //delete cached data
    
    function flush($key)
    {
        $keys = $this->getKeys($key);
        if(!empty($key)){
            return Cache::driver($this->connection)->deleteMultiple($keys);
        }

    }
    function flushMultiple($keys)
    {
        $allKeys = [];
        foreach ($keys as $key) {
            $allKeys = array_merge($allKeys,$this->getKeys($key));
        }
        return Cache::driver($this->connection)->deleteMultiple($allKeys);
    }

    //tags management
    public static function flushCacheTags($operation,$id = null){
        $tags = self::getCacheTags($operation,$id);
        foreach ($tags['database'] as $tag) {
            DB::table($tag['table'])->where($tag['key'],$tag['operation'] ?? "=" ,$tag['value'])->delete();
        }
        foreach ($tags['redis'] as $tag) {
           self::connection($tag['driver'])->flush($tag['key']);
        }
    }
    public static function getCacheTags($operation,$id = null){
        return [
            'database'  => self::getDatabaseCacheTags($operation,$id),
            'redis'     => self::getRedisCacheTags($operation,$id),
        ];
     
    }
    public static function getRedisCacheTags($operation,$id = null){
        $redisCacheTags = [
            'operation-key' => [
                [
                    'driver'        => "settings",
                    'key'           => "key",
                ],
            ],
            'update-settings' => [
                [
                    'driver'        => "settings",
                    'key'           => "all-database-settings",
                    'cond'          => true
                ],
                [
                    'driver'        => "settings",
                    'key'           => "user_settings:*",
                    'cond'          => in_array($id, config("backend-settings.flush_user_settings") ?? [])

                ],
                [
                    'driver'        => "settings",
                    'key'           => "user2_settings:*",
                    'cond'          => in_array($id, config("backend-settings.flush_user_settings") ?? [])
                ],
            ],
        ];
        return $redisCacheTags[$operation] ?? [];
    }
    public static function getDatabaseCacheTags($operation,$id = null){
        $databaseCacheTags = [
            'operation-key' => [
                [
                    'table'         => "users_meta",
                    'key'           => "key",
                    'operation'     => "Like",
                    'value'         => "%user_settings%",
                ],
            ],
            'update-settings' => [
                [
                    'table'         => "users_meta",
                    'key'           => "key",
                    'operation'     => "Like",
                    'value'         => "%user_settings%",
                    'cond'          => in_array($id, config("backend-settings.flush_user_settings") ?? [])
                ],
                [
                    'table'         => "users_meta",
                    'key'           => "key",
                    'operation'     => "Like",
                    'value'         => "%user2_settings%",
                    'cond'          => in_array($id, config("backend-settings.flush_user_settings") ?? [])

                ],
            ],
        ];
        return $databaseCacheTags[$operation] ?? [];
    }

}

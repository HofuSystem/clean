<?php 
namespace Core\Cache\Services;

use Illuminate\Support\Facades\Cache;
use Core\General\Helpers\ToolHelper;

class FileCacheManger implements Cacheable
{
    protected $connection   = "file";
    protected $path ;
    function __construct(){
        $this->path = storage_path('framework/cache/data'); 
    }
    public static function connection($connection) : self
    {
        $object             = (new self);
        return $object;
    }
    public function setConnection($connection): static
    {
       return $this;
    }

    function get($key)
    {

        return Cache::driver($this->connection)->get($key);
    }

    function getAllKeys() {
      $keys = $this->get('cache_keys') ?? [];
      return $keys;
    }
    function addKey($key) {
        $cacheKey   = 'cache_keys';
        $keys       = $this->get($cacheKey) ?? [];

        if(!in_array($key,$keys)){
            $keys[] = $key;
        }

        Cache::driver('file')->forget($cacheKey);
        Cache::driver('file')->rememberForever($cacheKey, self::dataFunc($keys));
        return $keys;
    }
    function removeKeys($removeKeys) {
        $cacheKey   = 'cache_keys';
        $keys       = $this->get($cacheKey) ?? [];

        $keys       = array_values(array_diff($keys, $removeKeys));
        foreach ($keys as  $index => $key) {
            if(!Cache::driver('file')->has($key)){
                unset($keys[$index]);
            }
        }
        $keys = array_filter($keys);
        Cache::driver('file')->forget($cacheKey);
        Cache::driver('file')->rememberForever($cacheKey, self::dataFunc($keys));
        return $keys;
    }
    function filterKeysByPattern(string $pattern, array $keys): array {
        $regex = '/^' . str_replace(
            ['*', '?', '[', ']'], 
            ['.*', '.', '\[', '\]'], 
            $pattern
        ) . '$/';
      
        return array_filter($keys, function ($key) use ($regex) {
            return preg_match($regex, $key);
        });
      }
    function getKeys($term) {

        $allKeys    = $this->getAllKeys(); 
        return $this->filterKeysByPattern($term,$allKeys);
    }

    //set cached data
    public function set($key,$data,$time = null){
        $this->addKey($key);
        if(isset($time)){
            $this->remember($key,$data,$time);
        }else{
            $this->rememberForever($key,$data);
        }
        $this->addKey($key);
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
        if(!empty($keys)){
            $this->removeKeys($keys);
            return Cache::driver($this->connection)->deleteMultiple($keys);
        }

    }
    function flushMultiple($keys)
    {
        $allKeys = [];
        foreach ($keys as $key) {
            $allKeys = array_merge($allKeys,$this->getKeys($key));
        }
        $this->removeKeys($keys);
        return Cache::driver($this->connection)->deleteMultiple($allKeys);
    }
}

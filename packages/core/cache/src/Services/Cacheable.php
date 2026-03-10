<?php 
namespace Core\Cache\Services;
interface Cacheable
{
    //set connections
    static function connection($connection);
    public function setConnection($connection);
    
    //get data
    public function get($key);

    //set data
    public function set($key,$data,$time = null);
    public function rememberForever($key, $data);
    public function remember($key,$data,$time);

    //get the keys
    public function getKeys($key);

    //flushing te data
    public function flush($key);
    public function flushMultiple($keys);
}

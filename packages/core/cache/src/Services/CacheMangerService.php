<?php

namespace Core\Cache\Services;


use Core\Entities\Helpers\EntityLoader;
use Core\General\Models\Setting;
use Core\Users\Models\UserMeta;

class CacheMangerService
{
    protected $connection = "default";
    public static function connection($connection) : self
    {
        $object             = (new self);
        $object->connection = $connection;
        return $object;
    }
    public  function setConnection($connection) : self
    {
        $this->connection = $connection;
        return $this;
    }
    public  function getService($for) 
    {
        $services = [
            'redis' => RedisCacheManger::class,
            'file'  => FileCacheManger::class,
        ];
       return $services[$for] ?? FileCacheManger::class;
    }

    //get cached data
    function get($key)
    {
        $service = $this->getService(config('cache.default'));
        return $service::connection($this->connection)->get($key);
    }

    function set($key,$data,$time = null)
    {  
        $service = $this->getService(config('cache.default'));
        return $service::connection($this->connection)->set($key,$data,$time);
    }

    function flush($key)
    {
        $service = $this->getService(config('cache.default'));
        return $service::connection($this->connection)->flush($key);
    }
    function flushMultiple($keys)
    {
        $service = $this->getService(config('cache.default'));
        return $service::connection($this->connection)->flushMultiple($keys);
    }
    public static function  clearEntity($entity)
    {
        $entity     = EntityLoader::load($entity);
        $instance   = (new static);
        $instance->setConnection('settings')->flush("entities:{$entity->slug}*");
        $instance->setConnection('apis')->flush("entities:{$entity->slug}*");
        $instance->setConnection('dashboard')->flush("entities:{$entity->slug}*");
    }
   
    public static function   clearEntityRecord($entity,$record)
    {
        //code...
        $entity     = EntityLoader::load($entity);
        $id         = is_numeric($record) ? $record : $record->id;
        $instance   = (new static);
        $instance->setConnection('settings')->flush("entities:{$entity->slug}:settings*");
        $instance->setConnection('apis')->flushMultiple(["entities:{$entity->slug}:api*","entities:{$entity->slug}:list*","entities:{$entity->slug}:single:" . $id . "*"]);
        $instance->setConnection('dashboard')->flushMultiple(["entities:{$entity->slug}:datatable*","entities:{$entity->slug}:html:" . $id . "*"]);
    }
    public static function flushUserCache(array $keys,$userIds)  {
        UserMeta::whereIn("key", $keys)->when(isset($userIds) and !empty($userIds),function($someUsersQuery)use($userIds){
            $someUsersQuery->whereIn('parent_id',$userIds);
        })->forceDelete();
        $instance   = (new static);
        if(empty($userIds) or !isset($userIds)){
            self::clearEntity('users');
            $instance->setConnection('settings')->flushMultiple(['settings:user_settings:*','settings:user2_settings:*']);
        }else{
            $userSettingsKeys = [];
            foreach ($userIds as $userId) {
                self::clearEntityRecord('users',$userId);
                $userSettingsKeys[] = 'settings:user_settings:'.$userId;
                $userSettingsKeys[] = 'settings:user2_settings:'.$userId;
            }
            $instance->setConnection('settings')->flushMultiple($userSettingsKeys);
        }
    } // end of flushSetting
    public static function flushSettingsCache()  {
        Setting::where('key','general_settings')->forceDelete();
        $instance   = (new static);
        self::flushUserCache(['user_settings','user2_settings'],null);
        $instance->setConnection('settings')->flush('*');
    } // end of flushSetting
    public static function flushSystemCache()  {
        Setting::where('key','general_settings')->forceDelete();
        $instance   = (new static);
        self::flushUserCache(['user_settings','user2_settings'],null);
        $instance->setConnection('dashboard')->flush('*');
        $instance->setConnection('apis')->flush('*');
        $instance->setConnection('blocks')->flush('*');
        $instance->setConnection('settings')->flush('*');
    } // end of flushSetting
}
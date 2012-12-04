<?php
/**
 * Base RedisRepository used for all other Repository Types
 *
 */

namespace Filth\RedisBundle\Entity;

use Predis\Client;
use Filth\RedisBundle\Entity\RedisEntityInterface;
use Filth\RedisBundle\Factory\RedisEntityFactory;


class BaseRedisRepository
{
    protected $redis = null;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Will return the Redis instance
     *
     * @return null|\Predis\Client
     */
    public function getRedis()
    {
        return $this->redis;
    }
	
    /**
     * Will delete given $key after $ttl seconds
     *
     */
    public function expire($key, $ttl)
    {
        $this->redis->expire($key, $ttl);
    }	

    /**
     * Will get a RedisEntity that matches the given key. The Key can be the BaseKey or a Key with values.
     * If there are values in key (f.e. eventpic_views_{EVENTID}_{PICTUREID}|123.456) then the value will be filled with these values.
     * They can be accessed with the Entity setters / getters.
     *
     * @param $key
     * @param RedisEntityFactory $fac
     * @return bool
     */
    public function getRedisEntityByKey($key, RedisEntityFactory $fac)
    {
        if($key == null) return false;

        // prepare key
        $baseKey = substr($key, 0, strpos($key, '|'));
        $values  = substr($key, strpos($key, '|')+1, strlen($key));

        // get entity with basekey
        $entity = $fac->getEntityByKey($baseKey);

        // fill values
        if($entity)
        {
            if($values)
            {
                $values = explode(BaseRedisEntity::VALUE_SEPARATOR, $values);

                // get list of required fields
                //$fields = $entity->getRequiredProperties();
                $fields = $entity->getProperties();

                // set values
                $i = 0;
                foreach($values as $value)
                {
                    if($value != '')
                    {
                        $fieldName = $fields[$i];
                        $methodName = 'set'.ucfirst($fieldName);
                        $entity->$methodName($value);
                    }
                    $i++;
                }
            }

            // get redis value
            $entity->setValue($this->redis->get($key));
        }

        return $entity;
    }
}
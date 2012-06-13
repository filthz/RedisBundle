<?php
/**
 * Contains all basic operations needed for Redis
 * No keys should be read / written directly from controllers, in order to avoid spreading the keys over the system.
 *
 */

namespace Filth\RedisBundle\Entity;

use Predis\Client;
use Filth\RedisBundle\Entity\RedisEntityInterface;
use Filth\RedisBundle\Factory\RedisEntityFactory;
use Filth\RedisBundle\Entity\BaseRedisRepository;


class RedisSetRepository extends BaseRedisRepository
{
    /**
     * Increases the value for the given RedisEntity by 1.
     * The key will be generated out of the data in the Entity
     *
     * @param Entity\RedisEntityInterface $redisEntity
     */
    public function increase(RedisEntityInterface $redisEntity)
    {
        // make sure all required fields are set in redis entity
        $redisEntity->validateRequiredFields();

        // increase key
        $this->redis->zincrby($redisEntity->getTable(), 1, $redisEntity->getFullKey());
    }

    /**
     * Will get all keys out Redis that are stored in the table defined in the given RedisEntity
     *
     * @param Entity\RedisEntityInterface $redisEntity
     */
    public function getKeys(RedisEntityInterface $redisEntity)
    {
        return $this->redis->zrange($redisEntity->getTable(), 0, -1);
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
                // get values
                $values = explode(BaseRedisEntity::VALUE_SEPARATOR, $values);

                // get list of required fields
                $fields = $entity->getRequiredProperties();

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
            $entity->setValue($this->redis->zscore($entity->getTable(), $key));
        }

        return $entity;
    }

    /**
     * Store entity in Redis
     *
     * @param Entity\RedisEntityInterface $redisEntity
     */
    public function save(RedisEntityInterface $redisEntity)
    {
        // make sure all required fields are set in redis entity
        $redisEntity->validateRequiredFields();

        $this->redis->zadd($redisEntity->getTable(), $redisEntity->getValue(), $redisEntity->getFullKey());
    }

    /**
     * Returns the value that is stored in the given entity
     *
     * @param Entity\RedisEntityInterface $redisEntity
     */
    public function get(RedisEntityInterface $redisEntity)
    {
        // make sure all required fields are set in redis entity
        $redisEntity->validateRequiredFields();

        return $this->redis->zscore($redisEntity->getTable(), $redisEntity->getFullKey());
    }

    /**
     * Delete an Entity from Redis
     *
     * @param Entity\RedisEntityInterface $redisEntity
     */
    public function delete(RedisEntityInterface $redisEntity)
    {
        // make sure all required fields are set in redis entity
        $redisEntity->validateRequiredFields();

        $this->redis->zrem($redisEntity->getTable(), $redisEntity->getFullKey());
    }

}

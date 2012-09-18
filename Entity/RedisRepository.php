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


class RedisRepository extends BaseRedisRepository
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
        $this->redis->incr($redisEntity->getFullKey());
    }

    /**
     * Decreases the value for the given RedisEntity by 1.
     * The key will be generated out of the data in the Entity
     *
     * @param Entity\RedisEntityInterface $redisEntity
     */
    public function decrease(RedisEntityInterface $redisEntity)
    {
        // make sure all required fields are set in redis entity
        $redisEntity->validateRequiredFields();

        // increase key
        $this->redis->decr($redisEntity->getFullKey());
    }

    /**
     * Will get all keys out Redis that can be created with the given RedisEntity
     *
     * @param Entity\RedisEntityInterface $redisEntity
     */
    public function getKeys(RedisEntityInterface $redisEntity)
    {
        // get base key
        $key = $redisEntity->getBaseKey();

        // add wildcard
        $key = $key.'*';

        // get keys
        return $this->redis->keys($key);
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

        $fullKey = $redisEntity->getFullKey();

        $this->redis->set($fullKey, $redisEntity->getValue());
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

        $fullKey = $redisEntity->getFullKey();

        return $this->redis->get($fullKey);
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

        $fullKey = $redisEntity->getFullKey();

        $this->redis->del($fullKey);
    }

}

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


class RedisHashRepository extends BaseRedisRepository
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
        $this->redis->hincrby($redisEntity->getTable(), 1, $redisEntity->getFullKey());
    }

    /**
     * Will get all keys out Redis that are stored in the table defined in the given RedisEntity
     *
     * @param Entity\RedisEntityInterface $redisEntity
     */
    public function getKeys(RedisEntityInterface $redisEntity)
    {
        return $this->redis->hkeys($redisEntity->getTable());
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

        $this->redis->hset($redisEntity->getTable(), $redisEntity->getValue(), $redisEntity->getFullKey());
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

        return $this->redis->hget($redisEntity->getTable(), $redisEntity->getFullKey());
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

        $this->redis->hdel($redisEntity->getTable(), $redisEntity->getFullKey());
    }

}

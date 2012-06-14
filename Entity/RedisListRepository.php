<?php
/**
 * Contains all basic operations needed for Redis Lists
 * No keys should be read / written directly from controllers, in order to avoid spreading the keys over the system.
 *
 */

namespace Filth\RedisBundle\Entity;

use Predis\Client;
use Filth\RedisBundle\Entity\RedisEntityInterface;
use Filth\RedisBundle\Factory\RedisEntityFactory;
use Filth\RedisBundle\Entity\BaseRedisRepository;


class RedisListRepository extends BaseRedisRepository
{

    /**
     * Will get all keys out Redis that are stored in the table defined in the given RedisEntity
     *
     * @param Entity\RedisEntityInterface $redisEntity
     */
    public function getKeys(RedisEntityInterface $redisEntity)
    {
        return $this->redis->LRANGE($redisEntity->getTable(), 0, -1);
    }

    /**
     * Insert all the specified $redisEntity at the tail of the list
     *
     * @param RedisEntityInterface $redisEntity
     */
    public function append(RedisEntityInterface $redisEntity)
    {
        $this->redis->RPUSH($redisEntity->getTable(), $redisEntity->getFullKey());
    }

    /**
     * Insert all the specified $redisEntity at the head of the list
     *
     * @param RedisEntityInterface $redisEntity
     */
    public function prepend(RedisEntityInterface $redisEntity)
    {
        $this->redis->LPUSH($redisEntity->getTable(), $redisEntity->getFullKey());
    }

    /**
     * Get the length of a list
     *
     * @param RedisEntityInterface $redisEntity
     */
    public function getLength(RedisEntityInterface $redisEntity)
    {
        $this->redis->LLEN($redisEntity->getTable());
    }

    /**
     * Returns the element at $index index in the list
     *
     * @param RedisEntityInterface $redisEntity
     * @param $index
     */
    public function getElementAt(RedisEntityInterface $redisEntity, $index)
    {
        $this->redis->LINDEX($redisEntity->getTable(), $index);
    }
}

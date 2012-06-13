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

}
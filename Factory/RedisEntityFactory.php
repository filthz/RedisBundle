<?php
/**
 * Factory class in order to get Redis Entitys. These are used by the RedisRepository then
 *
 * Example Usage in a controller:
 *
 * #/app/config/config.yml
 * filth_redis:
 *   entities:
 *       - { alias: EVENTPICTURE_VIEWS, class: Foo\Redis\EventPictureViewsRedisEntity }
 *
 *
 * $redisClient  = $this->get('snc_redis.default_client');
 * $redisFactory = $this->get('filth.redis.factory');
 * $redisRepo    = new RedisRepository($redisClient);
 *
 * $entity = $f->getRedisEntityByAlias('EVENTPICTURE_VIEWS');
 * $redisRepo->save($entity);
 *
 */

namespace Filth\RedisBundle\Factory;

use Symfony\Component\DependencyInjection\Container;
use Predis\Client;


class RedisEntityFactory
{
    // holds a mapping key -> entity class
    private $keyArray   = array();

    // holds a mapping alias -> entity class
    private $aliasArray = array();

    public function __construct(Container &$container)
    {
        $this->keyArray   = $container->getParameter('filth_redisbundle_entity_keys');
        $this->aliasArray = $container->getParameter('filth_redisbundle_entity_aliases');
    }

    /**
     * Returns an entity, which derives from the given BaseKey. (f.e. eventpic_views_{EVENTID}_{PICTUREID} )
     *
     * @param $baseKey
     */
    public function getEntityByKey($baseKey)
    {
        if(isset($this->keyArray[$baseKey])) return new $this->keyArray[$baseKey]($this);

        return false;
    }

    /**
     * Retuns a mapping with key - entityclass
     *
     * @return array
     */
    public function getKeyList()
    {
        return $this->keyArray;
    }

    /**
     * Returns an RedisEntity with the given alias. Aliases are defined in config.yml
     *
     * @param $alias
     * @return mixed
     * @throws \Exception
     */
    public function getEntityByAlias($alias)
    {
        if(isset($this->aliasArray[$alias])) return new $this->aliasArray[$alias]($this);

        throw new \Exception('Alias '.$alias.' ist nicht bekannt. Mögliche Enums sind: '.implode(', ', array_keys($this->aliasArray)));
    }
}
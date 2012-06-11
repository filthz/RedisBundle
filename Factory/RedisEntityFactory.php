<?php
/**
<<<<<<< HEAD
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
=======
 * Factory Klasse, um Redis Entitys von einem bestimmten Typ (siehe Enums) zu erzeugen. Diese werden von der RedisRepository verwendet
 *
 * Example Usage:
$f = new \VirtualNights\Common\DomainBundle\Redis\RedisEntityFactory();
$g = new \VirtualNights\Common\DomainBundle\Redis\RedisRepository($this->get('snc_redis.default_client'));
$c = $f->getRedisEntity($f::EventPictureViewsRedisEntity);
$g->save($c);
 *
 * Created by JetBrains PhpStorm.
 * User: Alex
 * Date: 23.05.12
 * Time: 15:15
 * To change this template use File | Settings | File Templates.
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
 */

namespace Filth\RedisBundle\Factory;

use Symfony\Component\DependencyInjection\Container;
use Predis\Client;


class RedisEntityFactory
{
<<<<<<< HEAD
    // holds a mapping key -> entity class
    private $keyArray   = array();

    // holds a mapping alias -> entity class
=======
    // hier ist ein mapping vorhanden - key zu EntityKlasse
    private $keyArray   = array();

    // hier ist ein mapping vorhanden - alias zu EntityKlasse
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
    private $aliasArray = array();

    public function __construct(Container &$container)
    {
        $this->keyArray   = $container->getParameter('filth_redisbundle_entity_keys');
        $this->aliasArray = $container->getParameter('filth_redisbundle_entity_aliases');
    }

    /**
<<<<<<< HEAD
     * Returns an entity, which derives from the given BaseKey. (f.e. eventpic_views_{EVENTID}_{PICTUREID} )
=======
     * Gibt die Entity, die sich aus dem übergebenen baseKey ableitet. Der Key muss aufbereitet übergeben werden (zB eventpic_views_{EVENTID}_{PICTUREID} )
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
     *
     * @param $baseKey
     */
    public function getEntityByKey($baseKey)
    {
        if(isset($this->keyArray[$baseKey])) return new $this->keyArray[$baseKey]($this);

        return false;
    }

    /**
<<<<<<< HEAD
     * Retuns a mapping with key - entityclass
=======
     * Gibt ein Mapping mit Key - EntityKlasse zurück
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
     *
     * @return array
     */
    public function getKeyList()
    {
        return $this->keyArray;
    }

    /**
<<<<<<< HEAD
     * Returns an RedisEntity with the given alias. Aliases are defined in config.yml
=======
     * Gibt eine RedisEntity über den Alias zurück, der in der cofig.yml definiert wird
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
     *
     * @param $alias
     * @return mixed
     * @throws \Exception
     */
    public function getRedisEntityByAlias($alias)
    {
<<<<<<< HEAD
=======
        // hiermit stellen wir sicher, dass nur valide enums verwendet werden
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
        if(isset($this->aliasArray[$alias])) return new $this->aliasArray[$alias]($this);

        throw new \Exception('Alias '.$alias.' ist nicht bekannt. Mögliche Enums sind: '.implode(', ', array_keys($this->aliasArray)));
    }
}
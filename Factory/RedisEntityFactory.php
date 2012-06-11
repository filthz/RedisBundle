<?php
/**
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
 */

namespace Filth\RedisBundle\Factory;

use Symfony\Component\DependencyInjection\Container;
use Predis\Client;


class RedisEntityFactory
{
    // hier ist ein mapping vorhanden - key zu EntityKlasse
    private $keyArray   = array();

    // hier ist ein mapping vorhanden - alias zu EntityKlasse
    private $aliasArray = array();

    public function __construct(Container &$container)
    {
        $this->keyArray   = $container->getParameter('filth_redisbundle_entity_keys');
        $this->aliasArray = $container->getParameter('filth_redisbundle_entity_aliases');
    }

    /**
     * Gibt die Entity, die sich aus dem übergebenen baseKey ableitet. Der Key muss aufbereitet übergeben werden (zB eventpic_views_{EVENTID}_{PICTUREID} )
     *
     * @param $baseKey
     */
    public function getEntityByKey($baseKey)
    {
        if(isset($this->keyArray[$baseKey])) return new $this->keyArray[$baseKey]($this);

        return false;
    }

    /**
     * Gibt ein Mapping mit Key - EntityKlasse zurück
     *
     * @return array
     */
    public function getKeyList()
    {
        return $this->keyArray;
    }

    /**
     * Gibt eine RedisEntity über den Alias zurück, der in der cofig.yml definiert wird
     *
     * @param $alias
     * @return mixed
     * @throws \Exception
     */
    public function getRedisEntityByAlias($alias)
    {
        // hiermit stellen wir sicher, dass nur valide enums verwendet werden
        if(isset($this->aliasArray[$alias])) return new $this->aliasArray[$alias]($this);

        throw new \Exception('Alias '.$alias.' ist nicht bekannt. Mögliche Enums sind: '.implode(', ', array_keys($this->aliasArray)));
    }
}
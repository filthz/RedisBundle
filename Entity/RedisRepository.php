<?php
/**
 * Beinhaltet alle Operationen, die Redis betreffen.
 * Es sollten keine Keys direkt aus Controllern gelesen / geschrieben werden um zu vermeiden, dass Redis-Keys auf das ganze System verteilt dezentral
 * eingesetzt werden
 *
 * Created by JetBrains PhpStorm.
 * User: Alex
 * Date: 23.05.12
 * Time: 15:15
 * To change this template use File | Settings | File Templates.
 */

namespace Filth\RedisBundle\Entity;

use Predis\Client;
use Filth\RedisBundle\Entity\RedisEntityInterface;


class RedisRepository
{
    private $redis = null;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Erhöht den Wert in Redis für die übergenene RedisEntity um 1.
     * Der Key wird aus den Informationen in der RedisEntity gebildet
     *
     * @param Entity\RedisEntityInterface $redisEntity
     */
    public function increase(RedisEntityInterface $redisEntity)
    {
        // make sure all required fields are set in redis entity
        $redisEntity->validateRequiredFields();

        // betroffenen key in redis erhöhen
        $this->redis->incr($redisEntity->getFullKey());
    }

    /**
     * Holt alle Keys aus Redis, die sich auf die übergebene redisEntity beziehen
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
     * Holt eine RedisEntity, die zum übergebenen key passt. Der Key kann nur der Base-Key sein oder ein Key, der Werte enthällt.
     *
     * Wenn der Key werte enthällt (zB eventpic_views_{EVENTID}_{PICTUREID}|123.456) dann wird die entity mit den Werten gefüllt.
     *
     * @param $key
     * @param RedisEntityFactory $fac
     * @return bool
     */
    public function getRedisEntityByKey($key, RedisEntityFactory $fac)
    {
        if($key == null) return false;

        // key aufbereiten
        $parts   = explode('|', $key);
        $baseKey = $parts[0];
        $values  = $parts[1];

        // entity mit dem baseKey holen
        $entity = $fac->getEntityByKey($baseKey);

        // entity mit werten füllen
        if($entity)
        {
            if($values)
            {
                // werte holen (sind punkt-getrennt)
                $values = explode('.', $values);

                // liste der required felder bekommen
                $fields = $entity->getRequiredProperties();

                // werte in die felder setzen
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

            // redis value holen
            $entity->setValue($this->redis->get($key));
        }

        return $entity;
    }

    /**
     * Speichert die Entity in Redis
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
     * Gibt den Wert zurück, der unter der gegebenen RedisEntity abgelegt ist
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
     * Löscht ein RedisEntity aus dem Speicher
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

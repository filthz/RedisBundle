<?php
/**
<<<<<<< HEAD
 * Contains all basic operations needed for Redis
 * No keys should be read / written directly from controllers, in order to avoid spreading the keys over the system.
 *
=======
 * Beinhaltet alle Operationen, die Redis betreffen.
 * Es sollten keine Keys direkt aus Controllern gelesen / geschrieben werden um zu vermeiden, dass Redis-Keys auf das ganze System verteilt dezentral
 * eingesetzt werden
 *
 * Created by JetBrains PhpStorm.
 * User: Alex
 * Date: 23.05.12
 * Time: 15:15
 * To change this template use File | Settings | File Templates.
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
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
<<<<<<< HEAD
     * Increases the value for the given RedisEntity by 1.
     * The key will be generated out of the data in the Entity
=======
     * Erhöht den Wert in Redis für die übergenene RedisEntity um 1.
     * Der Key wird aus den Informationen in der RedisEntity gebildet
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
     *
     * @param Entity\RedisEntityInterface $redisEntity
     */
    public function increase(RedisEntityInterface $redisEntity)
    {
        // make sure all required fields are set in redis entity
        $redisEntity->validateRequiredFields();

<<<<<<< HEAD
        // increase key
=======
        // betroffenen key in redis erhöhen
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
        $this->redis->incr($redisEntity->getFullKey());
    }

    /**
<<<<<<< HEAD
     * Will get all keys out Redis that can be created with the given RedisEntity
=======
     * Holt alle Keys aus Redis, die sich auf die übergebene redisEntity beziehen
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
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
<<<<<<< HEAD
     * Will get a RedisEntity that matches the given key. The Key can be the BaseKey or a Key with values.
     * If there are values in key (f.e. eventpic_views_{EVENTID}_{PICTUREID}|123.456) then the value will be filled with these values.
     * They can be accessed with the Entity setters / getters.
=======
     * Holt eine RedisEntity, die zum übergebenen key passt. Der Key kann nur der Base-Key sein oder ein Key, der Werte enthällt.
     *
     * Wenn der Key werte enthällt (zB eventpic_views_{EVENTID}_{PICTUREID}|123.456) dann wird die entity mit den Werten gefüllt.
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
     *
     * @param $key
     * @param RedisEntityFactory $fac
     * @return bool
     */
    public function getRedisEntityByKey($key, RedisEntityFactory $fac)
    {
        if($key == null) return false;

<<<<<<< HEAD
        // prepare key
=======
        // key aufbereiten
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
        $parts   = explode('|', $key);
        $baseKey = $parts[0];
        $values  = $parts[1];

<<<<<<< HEAD
        // get entity with basekey
        $entity = $fac->getEntityByKey($baseKey);

        // fill values
=======
        // entity mit dem baseKey holen
        $entity = $fac->getEntityByKey($baseKey);

        // entity mit werten füllen
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
        if($entity)
        {
            if($values)
            {
<<<<<<< HEAD
                // get values (. separated)
                $values = explode('.', $values);

                // get list of required fields
                $fields = $entity->getRequiredProperties();

                // set values
=======
                // werte holen (sind punkt-getrennt)
                $values = explode('.', $values);

                // liste der required felder bekommen
                $fields = $entity->getRequiredProperties();

                // werte in die felder setzen
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
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

<<<<<<< HEAD
            // get redis value
=======
            // redis value holen
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
            $entity->setValue($this->redis->get($key));
        }

        return $entity;
    }

    /**
<<<<<<< HEAD
     * Store entity in Redis
=======
     * Speichert die Entity in Redis
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
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
<<<<<<< HEAD
     * Returns the value that is stored in the given entity
=======
     * Gibt den Wert zurück, der unter der gegebenen RedisEntity abgelegt ist
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
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
<<<<<<< HEAD
     * Delete an Entity from Redis
=======
     * Löscht ein RedisEntity aus dem Speicher
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
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

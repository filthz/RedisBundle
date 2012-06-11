<?php

namespace Filth\RedisBundle\Validator;

/**
 * Created by JetBrains PhpStorm.
 * User: filth
 * Date: 10.06.12
 * Time: 12:43
 * To change this template use File | Settings | File Templates.
 */
use Filth\RedisBundle\Annotation\RedisAnnotation;

class EntityValidator
{
    private $entities = null;

    public function __construct($entities)
    {
        $this->entities = $entities;
    }

    public function validate($container)
    {
        $keyArray   = array();
        $aliasArray = array();

        foreach ($this->entities as $entity) {

            $reflectionClass = new \ReflectionClass($entity['class']);
            $reader = new \Doctrine\Common\Annotations\AnnotationReader();
            $annotation = $reader->getClassAnnotation($reflectionClass, new RedisAnnotation(array()));

            $redisKey = $annotation->getRedisKey();

            // und es wird geprüft, ob ein Key mehrfach definiert wurde. Falls ja, wird eine Exception geworfen
            if($redisKey != null)
            {
                if(!isset($keyArray[$redisKey]))
                {
                    $keyArray[$redisKey] = $entity['class'];

                    if(isset($aliasArray[$entity['alias']])) throw new \Exception('Alias '.$entity['alias'].' wurde mehrfach verwendet! Bitte korrigieren!');
                    $aliasArray[$entity['alias']] = $entity['class'];
                }
                else
                {
                    throw new \Exception('Redis Key: '.$redisKey.' wurde mehrfach definiert. Letztes Vorkommen möglicherweise in der Klasse: '.$entity['class'].'. Bitte korrigieren!');
                }
            }
        }

        $container->setParameter('filth_redisbundle_entity_keys', $keyArray);     // hier stehen key -> class mappings drin
        $container->setParameter('filth_redisbundle_entity_aliases', $aliasArray);// hier stehen alias -> class mappings drin
    }
}

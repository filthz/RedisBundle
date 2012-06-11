<?php

namespace Filth\RedisBundle\Validator;

/**
<<<<<<< HEAD
 * This checks if all redis keys and aliases are unique
 *
=======
 * Created by JetBrains PhpStorm.
 * User: filth
 * Date: 10.06.12
 * Time: 12:43
 * To change this template use File | Settings | File Templates.
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
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

<<<<<<< HEAD
            // check if a key or alias was redefined. if so throw an exception
=======
            // und es wird geprüft, ob ein Key mehrfach definiert wurde. Falls ja, wird eine Exception geworfen
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
            if($redisKey != null)
            {
                if(!isset($keyArray[$redisKey]))
                {
                    $keyArray[$redisKey] = $entity['class'];

<<<<<<< HEAD
                    if(isset($aliasArray[$entity['alias']])) throw new \Exception('Alias '.$entity['alias'].' was redefined. Please check!');
=======
                    if(isset($aliasArray[$entity['alias']])) throw new \Exception('Alias '.$entity['alias'].' wurde mehrfach verwendet! Bitte korrigieren!');
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
                    $aliasArray[$entity['alias']] = $entity['class'];
                }
                else
                {
<<<<<<< HEAD
                    throw new \Exception('Redis Key: '.$redisKey.' was redefined. Last seen in class: '.$entity['class'].'. Please fix!');
=======
                    throw new \Exception('Redis Key: '.$redisKey.' wurde mehrfach definiert. Letztes Vorkommen möglicherweise in der Klasse: '.$entity['class'].'. Bitte korrigieren!');
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
                }
            }
        }

        $container->setParameter('filth_redisbundle_entity_keys', $keyArray);     // hier stehen key -> class mappings drin
        $container->setParameter('filth_redisbundle_entity_aliases', $aliasArray);// hier stehen alias -> class mappings drin
    }
}

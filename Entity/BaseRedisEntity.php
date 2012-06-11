<?php
/**
<<<<<<< HEAD
 * Base implementation of an RedisEntity. All Entitys should extend this Class.
 * On Instantiation this will check, if the Annotations are used correctly.
 *
 *
=======
 * Created by JetBrains PhpStorm.
 * User: Alex
 * Date: 29.05.12
 * Time: 08:53
 * To change this template use File | Settings | File Templates.
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
 */

namespace Filth\RedisBundle\Entity;

use Filth\RedisBundle\Annotation\RedisAnnotation;

class BaseRedisEntity implements RedisEntityInterface
{
    private $key    = null;
    private $value  = null;

    public function __construct($called_from)
    {
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $annotation = $reader->getClassAnnotation(new \ReflectionClass($this), new RedisAnnotation(array()));

        if(!is_object($annotation) || $annotation->getRedisKey() == null) throw new \Exception('No RedisEntityKey defined in ('.get_class($this).'). Please use RedisEntityFactory for getting RedisEntitys or check the Factory class for Errors!');
        $this->key = $annotation->getRedisKey();

        // im key darf das Zeichen "|" und '*' nicht vorkommen!
<<<<<<< HEAD
        if(strpos($this->key, '|') !== false) throw new \Exception('The RedisKey may not contain the character \'|\'. Found in class: '.get_class($this));
        if(strpos($this->key, '*') !== false) throw new \Exception('The RedisKey may not contain the character \'*\'. Found in class: '.get_class($this));
        if(strpos($this->key, '.') !== false) throw new \Exception('The RedisKey may not contain the character \'.\'. Found in class: '.get_class($this));
=======
        if(strpos($this->key, '|') !== false) throw new \Exception('Der RedisKey darf das Zeichen | nicht enthalten!');
        if(strpos($this->key, '*') !== false) throw new \Exception('Der RedisKey darf das Zeichen * nicht enthalten!');
        if(strpos($this->key, '.') !== false) throw new \Exception('Der RedisKey darf das Zeichen . nicht enthalten!');
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f

        // make sure entity is build only from RedisEntityFactory!
        $class = explode('\\', get_class($called_from));
        $class = end($class);
<<<<<<< HEAD
        if($class != 'RedisEntityFactory' && $class != 'RedisRepository') throw new \Exception('Redis Entity cannot be instantiated directly. Please use RedisEntityFactory!');
=======
        if($class != 'RedisEntityFactory' && $class != 'RedisRepository') throw new \Exception('Redis Entity kann nicht direkt instanziiert werden! Sie müssen über die RedisEntityFactory Klasse geholt werden!');
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f

        // make sure entity has all the setters and getters!
        $requiredProps = $this->getRequiredProperties();
        $methods       = array_flip(get_class_methods($this));
        foreach($requiredProps as $field)
        {
<<<<<<< HEAD
            if(!isset($methods['set'.ucfirst($field)])) throw new \Exception('Missing method: '.'set'.ucfirst($field).' in Class: '.get_class($this) );
            if(!isset($methods['get'.ucfirst($field)])) throw new \Exception('Missing method: '.'set'.ucfirst($field).' in Class: '.get_class($this) );
=======
            if(!isset($methods['set'.ucfirst($field)])) throw new \Exception('Methode mit dem Namen: '.'set'.ucfirst($field).' fehlt in der Klasse: '.get_class($this) );
            if(!isset($methods['get'.ucfirst($field)])) throw new \Exception('Methode mit dem Namen: '.'set'.ucfirst($field).' fehlt in der Klasse: '.get_class($this) );
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
        }
    }

    /**
<<<<<<< HEAD
     * A value can be set here. If any is set, this will be stored under the generated key.
=======
     * Hier kann ein Value gesetzt werden. Wird einer gesetzt, dann wird dieser Wert unter dem generierten Schlüssel geschrieben
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
     *
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

<<<<<<< HEAD
    /**
     * Value getter
     *
     * @return null
     */
=======
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
    public function getValue()
    {
        return $this->value;
    }

<<<<<<< HEAD
    /**
     * Base key getter. A Base key is the defined key in the entity with an attached '|'. This separates the key
     * from all the values.
     *
     * @return string
     */
=======
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
    public function getBaseKey()
    {
        return $this->key.'|';
    }

    /**
<<<<<<< HEAD
     * Will return a list of fields, which are marked as required in the entity.
=======
     * Gibt eine Liste der Felder, die als required markiert sind
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
     *
     * @return array
     */
    public function getRequiredProperties()
    {
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $reflClass = new \ReflectionClass(get_class($this));

        $properties = $reflClass->getProperties();
        $requiredProperties = array();
        foreach($properties as $property)
        {
            $annotation = $reader->getPropertyAnnotation($property, get_class( new RedisAnnotation(array())) );

<<<<<<< HEAD
            // annotation is set
=======
            // annotation gesetzt
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
            if(is_object($annotation))
            {
                if($annotation->isRequired())
                {
                    $requiredProperties[] = $property->getName();
                }
            }
        }

        return $requiredProperties;
    }

    /**
<<<<<<< HEAD
     * Will create the full key including all the necessary values. Before calling this method  validateRequiredFields() should be called first!.
=======
     * Bildet den vollen Key inkl der benötigten Werte. Vor dem Aufruf dieser Methode sollte validateRequiredFields() aufgerufen werden!
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
     *
     * @return string
     */
    public function getFullKey()
    {
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $reflClass = new \ReflectionClass(get_class($this));

        $properties = $reflClass->getProperties();

        $fullKey = $this->getBaseKey();

        foreach($properties as $property)
        {
            $annotation = $reader->getPropertyAnnotation($property, get_class( new RedisAnnotation(array())) );

            // annotation gesetzt
            if(is_object($annotation))
            {
                if($annotation->isRequired())
                {
                    $propertyName = $property->getName();
                    $fullKey .= $this->$propertyName.'.';
                }
            }
        }

        return $fullKey;
    }

    /**
<<<<<<< HEAD
     * Will check, if all fields marked as "required" are set
=======
     * Prüft ob alle Felder mit der Annotation "required" tatsächlich gefüllt sind
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
     *
     * @return bool
     * @throws \Exception
     */
    public function validateRequiredFields()
    {
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $reflClass = new \ReflectionClass(get_class($this));

        $properties = $reflClass->getProperties();
        foreach($properties as $property)
        {
            $annotation = $reader->getPropertyAnnotation($property, get_class( new RedisAnnotation(array())) );

            // annotation gesetzt
            if(is_object($annotation))
            {
                if($annotation->isRequired())
                {
                    $propertyName = $property->getName();

<<<<<<< HEAD
                    if(! $property->isProtected() ) throw new \Exception('All fields that have an RedisAnnotation must be protected!
                                            Variable '.$propertyName.' in the class '.get_class($this).' is not protected!');

                    if( $this->$propertyName == null) throw new \Exception('Operation not possible as '.$propertyName.' in Class '.get_class($this).' ís marked as required but is not set!');
=======
                    if(! $property->isProtected() ) throw new \Exception('Alle Variablen, die die RedisAnnotation besitzen müssen als protected deklariert werden.
                                            Bei der Variablen '.$propertyName.' in der Klasse '.get_class($this).' ist es nicht der Fall!');

                    if( $this->$propertyName == null) throw new \Exception('Operation nicht möglich, da '.$propertyName.' in der Klasse '.get_class($this).' als required markiert ist, aber nicht gesetzt wurde!');
>>>>>>> 9c87867ecca2d06f3e0f33584732057af6f4759f
                }
            }
        }

        return true;
    }

}

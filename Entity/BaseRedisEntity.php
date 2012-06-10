<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alex
 * Date: 29.05.12
 * Time: 08:53
 * To change this template use File | Settings | File Templates.
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
        if(strpos($this->key, '|') !== false) throw new \Exception('Der RedisKey darf das Zeichen | nicht enthalten!');
        if(strpos($this->key, '*') !== false) throw new \Exception('Der RedisKey darf das Zeichen * nicht enthalten!');
        if(strpos($this->key, '.') !== false) throw new \Exception('Der RedisKey darf das Zeichen . nicht enthalten!');

        // make sure entity is build only from RedisEntityFactory!
        $class = explode('\\', get_class($called_from));
        $class = end($class);
        if($class != 'RedisEntityFactory' && $class != 'RedisRepository') throw new \Exception('Redis Entity kann nicht direkt instanziiert werden! Sie müssen über die RedisEntityFactory Klasse geholt werden!');

        // make sure entity has all the setters and getters!
        $requiredProps = $this->getRequiredProperties();
        $methods       = array_flip(get_class_methods($this));
        foreach($requiredProps as $field)
        {
            if(!isset($methods['set'.ucfirst($field)])) throw new \Exception('Methode mit dem Namen: '.'set'.ucfirst($field).' fehlt in der Klasse: '.get_class($this) );
            if(!isset($methods['get'.ucfirst($field)])) throw new \Exception('Methode mit dem Namen: '.'set'.ucfirst($field).' fehlt in der Klasse: '.get_class($this) );
        }
    }

    /**
     * Hier kann ein Value gesetzt werden. Wird einer gesetzt, dann wird dieser Wert unter dem generierten Schlüssel geschrieben
     *
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getBaseKey()
    {
        return $this->key.'|';
    }

    /**
     * Gibt eine Liste der Felder, die als required markiert sind
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

            // annotation gesetzt
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
     * Bildet den vollen Key inkl der benötigten Werte. Vor dem Aufruf dieser Methode sollte validateRequiredFields() aufgerufen werden!
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
     * Prüft ob alle Felder mit der Annotation "required" tatsächlich gefüllt sind
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

                    if(! $property->isProtected() ) throw new \Exception('Alle Variablen, die die RedisAnnotation besitzen müssen als protected deklariert werden.
                                            Bei der Variablen '.$propertyName.' in der Klasse '.get_class($this).' ist es nicht der Fall!');

                    if( $this->$propertyName == null) throw new \Exception('Operation nicht möglich, da '.$propertyName.' in der Klasse '.get_class($this).' als required markiert ist, aber nicht gesetzt wurde!');
                }
            }
        }

        return true;
    }

}

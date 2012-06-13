<?php
/**
 * Base implementation of an RedisEntity. All Entitys should extend this Class.
 * On Instantiation this will check, if the Annotations are used correctly.
 *
 *
 */

namespace Filth\RedisBundle\Entity;

use Filth\RedisBundle\Annotation\RedisAnnotation;

class BaseRedisEntity implements RedisEntityInterface
{
    private $key    = null;
    private $value  = null;
    private $table  = null;

    public function __construct($called_from)
    {
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $annotation = $reader->getClassAnnotation(new \ReflectionClass($this), new RedisAnnotation(array()));

        if(!is_object($annotation) || $annotation->getRedisKey() == null) throw new \Exception('No RedisEntityKey defined in ('.get_class($this).'). Please use RedisEntityFactory for getting RedisEntitys or check the Factory class for Errors!');
        $this->key = $annotation->getRedisKey();

        // set table name
        if(!is_object($annotation) || $annotation->getTable() != null) $this->table = $annotation->getTable();

        // im key darf das Zeichen "|" und '*' nicht vorkommen!
        if(strpos($this->key, '|') !== false) throw new \Exception('The RedisKey may not contain the character \'|\'. Found in class: '.get_class($this));
        if(strpos($this->key, '*') !== false) throw new \Exception('The RedisKey may not contain the character \'*\'. Found in class: '.get_class($this));
        if(strpos($this->key, '.') !== false) throw new \Exception('The RedisKey may not contain the character \'.\'. Found in class: '.get_class($this));

        // make sure entity is build only from RedisEntityFactory!
        $class = explode('\\', get_class($called_from));
        $class = end($class);
        if($class != 'RedisEntityFactory' && $class != 'RedisRepository') throw new \Exception('Redis Entity cannot be instantiated directly. Please use RedisEntityFactory!');

        // make sure entity has all the setters and getters!
        $requiredProps = $this->getRequiredProperties();
        $methods       = array_flip(get_class_methods($this));
        foreach($requiredProps as $field)
        {
            if(!isset($methods['set'.ucfirst($field)])) throw new \Exception('Missing method: '.'set'.ucfirst($field).' in Class: '.get_class($this) );
            if(!isset($methods['get'.ucfirst($field)])) throw new \Exception('Missing method: '.'set'.ucfirst($field).' in Class: '.get_class($this) );
        }
    }

    /**
     * A value can be set here. If any is set, this will be stored under the generated key.
     *
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Value getter
     *
     * @return null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Base key getter. A Base key is the defined key in the entity with an attached '|'. This separates the key
     * from all the values.
     *
     * @return string
     */
    public function getBaseKey()
    {
        return $this->key.'|';
    }

    /**
     * Will return a list of fields, which are marked as required in the entity.
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

            // annotation is set
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
     * Will create the full key including all the necessary values. Before calling this method  validateRequiredFields() should be called first!.
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
     * Will check, if all fields marked as "required" are set
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

                    if(! $property->isProtected() ) throw new \Exception('All fields that have an RedisAnnotation must be protected!
                                            Variable '.$propertyName.' in the class '.get_class($this).' is not protected!');

                    if( $this->$propertyName == null) throw new \Exception('Operation not possible as '.$propertyName.' in Class '.get_class($this).' ís marked as required but is not set!');
                }
            }
        }

        return true;
    }

    public function getTable()
    {
        return $this->table;
    }

}

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

    const VALUE_SEPARATOR = '^FRS^';

    public function __construct($called_from)
    {
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $annotation = $reader->getClassAnnotation(new \ReflectionClass($this), new RedisAnnotation(array()));

        if(!is_object($annotation) || $annotation->getRedisKey() == null) throw new \Exception('No RedisEntityKey defined in ('.get_class($this).'). Please use RedisEntityFactory for getting RedisEntitys or check the Factory class for Errors!');
        $this->key = $annotation->getRedisKey();

        // set table name
        if(!is_object($annotation) || $annotation->getTable() != null)
        {
            // make sure all variables in tablename are used correctly
            if( substr_count($annotation->getTable(), '{') != substr_count($annotation->getTable(), '}') )
                throw new \Exception('Unclosed or unopened variable in Tablename. Make sure all Variables in Tablename are used like {FOO}!');

            $this->table = $annotation->getTable();
        }

        // im key darf das Zeichen "|" und '*' nicht vorkommen!
        if(strpos($this->key, '|') !== false) throw new \Exception('The RedisKey may not contain the character \'|\'. Found in class: '.get_class($this));
        if(strpos($this->key, '*') !== false) throw new \Exception('The RedisKey may not contain the character \'*\'. Found in class: '.get_class($this));

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
     * Returns all properties of an Entity
     *
     * @return array
     */
    public function getProperties()
    {
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $reflClass = new \ReflectionClass(get_class($this));

        $properties = $reflClass->getProperties();
        $requiredProperties = array();
        foreach($properties as $property)
        {
            $annotation = $reader->getPropertyAnnotation($property, get_class( new RedisAnnotation(array())) );

            // annotation is set
            if(is_object($annotation)) $requiredProperties[] = $property->getName();
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
                $propertyName = $property->getName();
                $fullKey .= $this->$propertyName.self::VALUE_SEPARATOR;
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

                    if( $this->$propertyName === null) throw new \Exception('Operation not possible as '.$propertyName.' in Class '.get_class($this).' ís marked as required but is not set!');
                }
            }
        }

        return true;
    }

    /**
     * This will inject variable values into table name if required
     *
     * @throws \Exception
     */
    private function prepareTableName()
    {
        $offset = 0;
        $table  = strtolower($this->table);

        while(substr_count($this->table, '{', $offset) > 0)
        {
            $offset = strpos($this->table, '{', $offset);
            $next  = strpos($this->table, '}', $offset);

            $varName = substr($this->table, $offset + 1 , $next - $offset - 1 ) ;

            if(!isset($this->$varName)) throw new \Exception('Entity '.get_class($this).' has an dynamic Tablename, but required value "'.$varName.'" is not set!');
            $table = str_replace('{'.$varName.'}', $this->$varName, $table);

            $offset = $offset + 1;
        }

        $this->table = $table;
    }

    /**
     * Will return the tablename of the Entity.
     *
     * @return null
     */
    public function getTable()
    {
        // inject table name variables if needed
        $this->prepareTableName();

        return $this->table;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

}

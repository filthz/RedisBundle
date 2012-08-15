<?php
namespace Filth\RedisBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/** @Annotation */
class RedisAnnotation extends Annotation
{
    public $required  = false;
    public $redis_key = null;
    public $table     = null;

    public function isRequired()
    {
        return $this->required;
    }

    public function getRedisKey()
    {
        return $this->redis_key;
    }

    public function getTable()
    {
        return $this->table;
    }
}

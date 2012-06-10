<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Alex
 * Date: 29.05.12
 * Time: 09:40
 * To change this template use File | Settings | File Templates.
 */
namespace Filth\RedisBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

class RedisAnnotation extends Annotation
{
    public $required  = false;
    public $redis_key = null;

    public function isRequired()
    {
        return $this->required;
    }

    public function getRedisKey()
    {
        return $this->redis_key;
    }
}

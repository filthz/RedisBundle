<?php

namespace Filth\RedisBundle\Entity;

interface RedisEntityInterface
{
    public function __construct($called_from);
    public function getBaseKey();
    public function getFullKey();
    public function getValue();
    public function setValue($value);
    public function getTable();
    public function validateRequiredFields();
}
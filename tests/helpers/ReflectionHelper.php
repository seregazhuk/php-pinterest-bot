<?php

namespace seregazhuk\tests\helpers;

use ReflectionClass;

trait ReflectionHelper
{
    /**
     * @var ReflectionClass
     */
    protected $reflection;

    protected $reflectedObject;

    /**
     * @param $property
     * @return mixed
     */
    public function getProperty($property)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($this->reflectedObject);
    }

    /**
     * @param $property
     * @param $value
     */
    public function setProperty($property, $value)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        $property->setValue($this->reflectedObject, $value);
    }

    /**
     * @param mixed $reflectedObject
     */
    public function setReflectedObject($reflectedObject)
    {
        $this->reflectedObject = $reflectedObject;
    }
}
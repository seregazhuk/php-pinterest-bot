<?php

namespace seregazhuk\tests\helpers;

use ReflectionClass;

trait ReflectionHelper
{
    /**
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * @param $property
     * @param $reflectedObject
     * @return mixed
     */
    public function getProperty($property, $reflectedObject)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($reflectedObject);
    }

    /**
     * @param $reflectedObject
     * @param $property
     * @param $value
     */
    public function setProperty($reflectedObject, $property, $value)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        $property->setValue($reflectedObject, $value);
    }
}
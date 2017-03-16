<?php

namespace seregazhuk\tests\Helpers;

use ReflectionClass;

/**
 * Class ReflectionHelper
 *
 * Helper for getting access to protected and private properties.
 */
trait ReflectionHelper
{
    /**
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * @var object
     */
    protected $reflectedObject;

    /**
     * @param $property
     *
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
     * @param mixed $object
     */
    public function setReflectedObject($object)
    {
        $this->reflection = new ReflectionClass($object);
        $this->reflectedObject = $object;
    }
}

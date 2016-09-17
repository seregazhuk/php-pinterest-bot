<?php

namespace seregazhuk\tests\Helpers;

use ReflectionClass;

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
     * @return $this
     */
    protected function setUpReflection()
    {
        $this->reflection = new ReflectionClass($this->provider);
        $this->setReflectedObject($this->provider);
        $this->setProperty('request', $this->request);

        return $this;
    }

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
     * @param mixed $reflectedObject
     */
    public function setReflectedObject($reflectedObject)
    {
        $this->reflectedObject = $reflectedObject;
    }
}

<?php

namespace seregazhuk\tests;


use seregazhuk\PinterestBot\Request;
use seregazhuk\PinterestBot\Providers\Provider;
use seregazhuk\PinterestBot\Http;
use PHPUnit_Framework_TestCase;

abstract class ProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Provider;
     */
    protected $provider;

    /**
     * @var \Mockable
     */
    protected $mock;

    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Request
     */
    protected function createRequestMock()
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->setMethods(['checkLoggedIn', 'exec'])
            ->setConstructorArgs([new Http()])
            ->getMock();
        $requestMock->method('checkLoggedIn')->willReturn(true);

        return $requestMock;
    }

    protected function setUp()
    {
        $this->reflection = new \ReflectionClass($this->provider);
    }

    protected function tearDown()
    {
        $this->provider   = null;
        $this->reflection = null;
    }

    public function getProperty($property)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($this->provider);
    }

    public function setProperty($property, $value)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        $property->setValue($this->provider, $value);
    }

    /**
     * Creates a response from Pinterest
     * @param array $data
     * @return array
     */
    protected function createApiResponse($data = [])
    {
        return array('resource_response' => $data);
    }

}
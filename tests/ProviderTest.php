<?php

namespace seregazhuk\tests;

use Mockable;
use ReflectionClass;
use seregazhuk\PinterestBot\Request;
use seregazhuk\PinterestBot\Providers\Provider;
use seregazhuk\PinterestBot\Http;
use PHPUnit_Framework_TestCase;
use seregazhuk\tests\helpers\ReflectionHelper;

/**
 * Class ProviderTest
 * @package seregazhuk\tests
 * @property Provider        $provider
 * @property string          $providerClass
 * @property Mockable        $mock
 * @property ReflectionClass $reflection
 */
abstract class ProviderTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelper;

    protected $provider;
    protected $providerClass;
    protected $mock;

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
        $this->createProviderInstance();
        $this->reflection = new ReflectionClass($this->provider);
        $this->mock = $this->createRequestMock();
        $this->setReflectedObject($this->provider);
        parent::setUp();
    }

    protected function tearDown()
    {
        $this->provider   = null;
        $this->reflection = null;
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

    protected function createProviderInstance()
    {
        $providerReflection = new ReflectionClass($this->providerClass);
        $this->provider = $providerReflection->newInstanceArgs([$this->createRequestMock()]);
    }
}
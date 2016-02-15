<?php

namespace seregazhuk\tests;

use Mockery;
use Mockery\MockInterface;
use ReflectionClass;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\tests\helpers\ResponseHelper;
use PHPUnit_Framework_MockObject_MockObject;
use seregazhuk\tests\helpers\ReflectionHelper;
use seregazhuk\PinterestBot\Api\Providers\Provider;

/**
 * Class ProviderTest
 * @package seregazhuk\tests
 * @property Provider $provider
 * @property string $providerClass
 * @property MockInterface $mock
 * @property ReflectionClass $reflection
 */
abstract class ProviderTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelper, ResponseHelper;

    protected $provider;
    protected $providerClass = Provider::class;
    protected $mock;

    /**
     * @return $this|Request
     */
    protected function createRequestMock()
    {
        $methods = array_merge(['exec', 'checkLoggedIn', 'isLoggedIn', 'followMethodCall']);
        $requestMock = Mockery::mock(Request::class)->shouldReceive($methods)->getMock();
        $requestMock->shouldReceive('checkLoggedIn')->andReturn(true);

        $this->mock = $requestMock;

        return $this;
    }

    protected function setUp()
    {
        $this->createRequestMock()->createProviderInstance()->setUpReflection();
        parent::setUp();
    }

    protected function tearDown()
    {
        Mockery::close();
        $this->provider = null;
        $this->reflection = null;
    }

    /**
     * @return static
     */
    protected function createProviderInstance()
    {
        $providerReflection = new ReflectionClass($this->providerClass);
        $this->provider = $providerReflection->newInstanceArgs(
            [$this->mock, new Response()]
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function setUpReflection()
    {
        $this->reflection = new ReflectionClass($this->provider);
        $this->setReflectedObject($this->provider);
        $this->setProperty('request', $this->mock);

        return $this;
    }
}
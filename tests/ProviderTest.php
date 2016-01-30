<?php

namespace seregazhuk\tests;

use Mockable;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\CurlAdapter;
use seregazhuk\tests\helpers\ResponseHelper;
use seregazhuk\tests\helpers\ReflectionHelper;
use seregazhuk\PinterestBot\Api\Providers\Provider;

/**
 * Class ProviderTest
 * @package seregazhuk\tests
 * @property Provider $provider
 * @property string $providerClass
 * @property PHPUnit_Framework_MockObject_MockObject $mock
 * @property ReflectionClass $reflection
 */
abstract class ProviderTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelper, ResponseHelper;

    protected $provider;
    protected $providerClass = Provider::class;
    protected $mock;

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Request
     */
    protected function createRequestMock()
    {
        $methods = array_merge(['exec', 'checkLoggedIn', 'isLoggedIn']);
        $requestMock = $this->getMockBuilder(Request::class)->setMethods($methods)->setConstructorArgs([new CurlAdapter()])->getMock();
        $requestMock->method('checkLoggedIn')->willReturn(true);

        return $requestMock;
    }

    protected function setUp()
    {
        $this->createProviderInstance();
        $this->reflection = new ReflectionClass($this->provider);
        $this->mock = $this->createRequestMock();
        $this->setReflectedObject($this->provider);
        $this->setProperty('request', $this->mock);
        parent::setUp();
    }

    protected function tearDown()
    {
        $this->provider = null;
        $this->reflection = null;
    }

    protected function createProviderInstance()
    {
        $providerReflection = new ReflectionClass($this->providerClass);
        $this->provider = $providerReflection->newInstanceArgs(
            [$this->createRequestMock(), new Response()]
        );
    }
}
<?php

namespace seregazhuk\tests\Api;

use Mockery;
use Mockery\Mock;
use ReflectionClass;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\tests\Helpers\ResponseHelper;
use seregazhuk\tests\Helpers\ReflectionHelper;
use seregazhuk\PinterestBot\Api\Providers\Provider;

/**
 * Class ProviderTest.
 *
 * @property string $providerClass
 * @property Mock $requestMock
 * @property ReflectionClass $reflection
 */
abstract class ProviderTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelper, ResponseHelper;

    protected $httpMockMethods = ['exec', 'checkLoggedIn', 'isLoggedIn', 'followMethodCall'];
    protected $providerClass = Provider::class;

    /**
     * @var Provider
     */
    protected $provider;

    /**
     * @var MockInterface
     */
    protected $mock;

    /**
     * @return $this
     */
    protected function createRequestMock()
    {
        $requestMock = Mockery::mock(Request::class)->shouldReceive($this->httpMockMethods)->getMock();

        $requestMock->shouldReceive('checkLoggedIn')->andReturn(true);
        
        $this->requestMock = $requestMock;

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
     * @return $this
     */
    protected function createProviderInstance()
    {
        $providerReflection = new ReflectionClass($this->providerClass);
        $this->provider = $providerReflection->newInstanceArgs([$this->requestMock, new Response()]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function setUpReflection()
    {
        $this->reflection = new ReflectionClass($this->provider);
        $this->setReflectedObject($this->provider);
        $this->setProperty('request', $this->requestMock);

        return $this;
    }

    protected function setResponse($response, $times = 1, $method = 'exec')
    {
        $this->requestMock->shouldReceive($method)->times($times)->andReturn($response);
    }

    protected function setSuccessResponse($times = 1)
    {
        $this->setResponse($this->createSuccessApiResponse(), $times);
    }

    protected function setErrorResponse($times = 1)
    {
        $this->setResponse($this->createErrorApiResponse(), $times);
    }

}

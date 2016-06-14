<?php

namespace seregazhuk\tests;

use Mockery;
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
 * @property MockInterface $mock
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
     * @return $this|Request
     */
    protected function createRequestMock()
    {
        $requestMock = Mockery::mock(Request::class)->shouldReceive($this->httpMockMethods)->getMock();
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
     * @return $this
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

    protected function setResponse($response, $times = 1, $method = 'exec')
    {        
        $this->mock->shouldReceive($method)->times($times)->andReturn($response);
    }

    protected function setSuccessResponse()
    {
        $this->setResponse($this->createSuccessApiResponse());
    }

    protected function setErrorResponse()
    {
        $this->setResponse($this->createErrorApiResponse());
    }

}

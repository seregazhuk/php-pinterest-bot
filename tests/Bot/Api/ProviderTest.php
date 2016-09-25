<?php

namespace seregazhuk\tests\Bot\Api;

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
 * @property ReflectionClass $reflection
 */
abstract class ProviderTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelper, ResponseHelper;

    /**
     * @var string
     */
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
     * @var Response
     */
    protected $response;

    /**
     * @var Request|MockInterface
     */
    protected $request;

    /**
     * @return $this
     */
    protected function createRequestMock()
    {
        $this->request = Mockery::mock(Request::class)
            ->shouldReceive('checkLoggedIn')
            ->andReturn(true)
            ->getMock();

        return $this;
    }

    /**
     * @return $this
     */
    protected function createResponseMock()
    {
        $this->response = Mockery::mock(Response::class)
            ->shouldDeferMissing();

        return $this;
    }

    protected function setUp()
    {
        $this->createRequestMock()
            ->createResponseMock()
            ->createProviderInstance()
            ->setUpReflection();

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
        $this->provider = $providerReflection->newInstanceArgs([$this->request, $this->response]);

        return $this;
    }
}

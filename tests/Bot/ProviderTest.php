<?php

namespace seregazhuk\tests\Bot\Api;

use Mockery;
use ReflectionClass;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;

/**
 * Class ProviderTest.
 *
 * @property string $providerClass
 * @property ReflectionClass $reflection
 */
class ProviderTest extends PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_returns_data_for_response_without_pagination()
    {
        $response = ['resource_response' => ['data' => 'value']];

        $request = Mockery::mock(Request::class)
            ->shouldReceive('exec')
            ->andReturn(json_encode($response))
            ->getMock();

        /** @var Provider $provider */
        $provider = Mockery::mock(Provider::class, [$request, new Response()])
            ->makePartial();

        $responseData = $response['resource_response']['data'];

        $this->assertEquals($responseData, $provider->visitPage());
    }

    /** @test */
    public function it_returns_response_object_if_it_has_pagination()
    {
        $response = ['resource' => ['options' => ['bookmarks' => 'bookmarks_string']]];

        $request = Mockery::mock(Request::class)
            ->shouldReceive('exec')
            ->andReturn(json_encode($response))
            ->getMock();

        /** @var Provider $provider */
        $provider = Mockery::mock(Provider::class, [$request, new Response()])
            ->makePartial();

        $this->assertInstanceOf(Response::class, $provider->visitPage());
    }
}

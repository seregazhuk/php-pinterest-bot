<?php

namespace seregazhuk\tests\Bot\Providers;

use Mockery;
use Mockery\MockInterface;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

trait ApiRequestAssertions
{
    /**
     * @var Request|MockInterface
     */
    protected $request;

    /**
     * @var HttpClient|MockInterface
     */
    protected $httpClient;

    protected function setUp()
    {
        parent::setUp();
        $this->request = Mockery::spy(Request::class);
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    /**
     * @param string $url
     * @param array $data
     */
    protected function assertWasPostRequest($url, array $data = [])
    {
        $postString = $this->request->createQuery($data);

        $check = function($requestUrl, $requestString) use ($url, $postString) {
            return $requestUrl === UrlBuilder::buildApiUrl($url) && $requestString === $postString;
        };

        $this->httpClient
            ->shouldHaveReceived('execute')
            ->withArgs($check);
    }

    /**
     * @param string $url
     * @param array $data
     */
    protected function assertWasGetRequest($url, array $data = [])
    {
        $query = empty($data) ? '' : '?' . $this->request->createQuery($data);

        $check = function($requestUrl) use ($url, $query) {
            return $requestUrl === UrlBuilder::buildApiUrl($url) . $query;
        };

        $this->httpClient
            ->shouldHaveReceived('execute')
            ->withArgs($check);
    }
}

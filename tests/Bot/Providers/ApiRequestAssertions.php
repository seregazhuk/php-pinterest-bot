<?php

namespace seregazhuk\tests\Bot\Providers;

use Mockery;
use Mockery\MockInterface;
use seregazhuk\PinterestBot\Api\Request;

trait ApiRequestAssertions
{
    /**
     * @var Request|MockInterface
     */
    protected $request;

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

        $this->request
            ->shouldHaveReceived('exec')
            ->withArgs([$url, $postString]);
    }

    /**
     * @param string $url
     * @param array $data
     */
    protected function assertWasGetRequest($url, array $data = [])
    {
        $query = $this->request->createQuery($data);

        $this->request
            ->shouldHaveReceived('exec')
            ->with($url . '?' . $query, '');
    }

    /**
     * @return MockInterface|Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}

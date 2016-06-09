<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Api\Providers\Pins;

/**
 * Class PinsTest.
 */
class PinsTest extends ProviderTest
{
    /**
     * @var Pins
     */
    protected $provider;
    protected $providerClass = Pins::class;

    /** @test */
    public function likeAPin()
    {
        $this->setSuccessResponse();
        $this->assertTrue($this->provider->like(1111));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->like(1111));
    }

    /** @test */
    public function unlikeAPin()
    {
        $this->setSuccessResponse();
        $this->assertTrue($this->provider->unLike(1111));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->unLike(1111));
    }

    /** @test */
    public function createCommentForPin()
    {
        $this->setSuccessResponse();
        $this->assertNotEmpty($this->provider->comment(1111, 'comment text'));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->comment(1111, 'comment text'));
    }

    /** @test */
    public function deleteCommentFromPin()
    {
        $this->setSuccessResponse();
        $this->assertTrue($this->provider->deleteComment(1111, 1111));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->deleteComment(1111, 1111));
    }

    /** @test */
    public function createANewPin()
    {
        $response = $this->createPinCreationResponse();
        $this->setResponse($response);

        $pinSource = 'http://example.com/image.jpg';
        $pinDescription = 'Pin Description';
        $boardId = 1;
        $this->assertNotFalse($this->provider->create($pinSource, $boardId, $pinDescription));

        $this->setResponse(null);
        $this->assertFalse($this->provider->create($pinSource, $boardId, $pinDescription));
    }

    /** @test */
    public function createARepin()
    {
        $response = $this->createPinCreationResponse();
        $this->setResponse($response);

        $boardId = 1;
        $repinId = 11;
        $pinDescription = 'Pin Description';

        $this->assertNotFalse($this->provider->repin($repinId, $boardId, $pinDescription));
        
        $this->setErrorResponse();
        $this->assertFalse($this->provider->repin($repinId, $boardId, $pinDescription));
    }

    /** @test */
    public function editPin()
    {
        $response = $this->createApiResponse();
        $this->setResponse($response);
        $this->assertNotFalse($this->provider->edit(1, 'new', 'changed'));

        $this->setResponse($this->createErrorApiResponse());
        $this->assertFalse($this->provider->edit(1, 'new', 'changed'));
    }

    /** @test */
    public function deletePin()
    {
        $response = $this->createApiResponse();
        $this->setResponse($response);
        $this->assertNotFalse($this->provider->delete(1));

        $this->setResponse($this->createErrorApiResponse());
        $this->assertFalse($this->provider->delete(1));
    }

    /** @test */
    public function getPinInfo()
    {
        $response = $this->createApiResponse();
        $this->setResponse($response);
        $this->assertNotNull($this->provider->info(1));

        $this->setResponse($this->createErrorApiResponse());
        $this->assertFalse($this->provider->info(1));
    }

    /** @test */
    public function searchForPins()
    {
        $response['module']['tree']['data']['results'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $expectedResultsNum = count($response['module']['tree']['data']['results']);
        $this->setResponse($response, 2);

        $res = iterator_to_array($this->provider->search('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res[0]);
    }

    /**
     * Creates a pin creation response from Pinterest.
     *
     * @return array
     */
    protected function createPinCreationResponse()
    {
        $data = ['data' => ['id' => 1]];

        return $this->createApiResponse($data);
    }

    /**
     * Creates a response from Pinterest.
     *
     * @param array $data
     *
     * @return array
     */
    protected function createApiResponse($data = ['data' => 'success'])
    {
        return parent::createApiResponse($data);
    }
}

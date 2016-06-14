<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Api\Providers\Boards;
use seregazhuk\tests\Helpers\FollowResponseHelper;

/**
 * Class BoardsTest.
 */
class BoardsTest extends ProviderTest
{
    use FollowResponseHelper;
    /**
     * @var Boards
     */
    protected $provider;
    protected $providerClass = Boards::class;

    /** @test */
    public function searchForBoards()
    {
        $response['module']['tree']['data']['results'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $expectedResultsNum = count($response['module']['tree']['data']['results']);
        $this->setResponse($response, 2);

        $res = iterator_to_array($this->provider->search('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res);
    }

    /** @test */
    public function followBoard()
    {
        $this->setFollowSuccessResponse();
        $this->assertTrue($this->provider->follow(1));

        $this->setFollowErrorResponse();
        $this->assertFalse($this->provider->follow(1));
    }

    /** @test */
    public function unFollowBoard()
    {
        $this->setFollowSuccessResponse();
        $this->assertTrue($this->provider->unFollow(1));

        $this->setFollowErrorResponse();
        $this->assertFalse($this->provider->unFollow(1));
    }

    /** @test */
    public function getBoardFollowers()
    {
        $response = $this->createPaginatedResponse();
        $this->setResponse($response);
        $this->setResponse((['resource_response' => ['data' => []]]));
        $this->setResponse([
                'resource_response' => [
                    'data' => [
                        ['type' => 'module'],
                    ],
                ],
            ]
        );

        $followers = $this->provider->followers(111);
        $this->assertCount(2, iterator_to_array($followers));

        $followers = $this->provider->followers(111);
        $this->assertEmpty(iterator_to_array($followers));
    }

    /** @test */
    public function getBoardsForSpecifiedUser()
    {
        $boards = ['data' => 'boards'];
        $response = $this->createApiResponse($boards);

        $this->setResponse($response);
        $this->assertEquals($boards['data'], $this->provider->forUser(1));

        $this->setResponse(null);
        $this->assertFalse($this->provider->forUser(1));
    }

    /** @test */
    public function getBoardInfo()
    {
        $response = $this->createApiResponse(['data' => 'info']);

        $this->setResponse($response);
        $this->assertEquals('info', $this->provider->info('username', 'board'));
        
        $this->setResponse(null);
        $this->assertFalse($this->provider->info('username', 'board'));
    }

    /** @test */
    public function getPinsFromBoard()
    {
        $response = $this->createPaginatedResponse();

        $this->setResponse($response);
        $this->setResponse((['resource_response' => ['data' => []]]));
        $this->setResponse([
                'resource_response' => [
                    'data' => [
                        ['type' => 'module'],
                    ],
                ],
            ]);

        $pins = $this->provider->pins(1);
        $this->assertCount(2, iterator_to_array($pins));

        $pins = $this->provider->pins(0);
        $this->assertEmpty(iterator_to_array($pins));
    }

    /** @test */
    public function deleteBoard()
    {
        $this->setSuccessResponse(); 
        $this->assertTrue($this->provider->delete(1111));

        $this->setErrorResponse();        
        $this->assertFalse($this->provider->delete(1111));
    }

    /** @test */
    public function createBoard()
    {
        $this->setSuccessResponse();
        $this->assertTrue($this->provider->create('test', 'test'));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->create('test', 'test'));
    }
}

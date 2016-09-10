<?php

namespace seregazhuk\tests\Api;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
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

    /**
     * @var string
     */
    protected $providerClass = Boards::class;

    /** @test */
    public function it_should_should_return_generator_when_searching()
    {
        $response['module']['tree']['data']['results'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $expectedResultsNum = count($response['module']['tree']['data']['results']);
        $this->setResponseExpectation($response);

        $res = iterator_to_array($this->provider->search('dogs', 2));
        $this->assertCount($expectedResultsNum, $res);
    }

    /** @test */
    public function it_should_follow_boards()
    {
        $boardId = 1;
        $this->setFollowSuccessResponse($boardId, UrlBuilder::RESOURCE_FOLLOW_BOARD);
        $this->assertTrue($this->provider->follow($boardId));

        $this->setFollowErrorResponse($boardId, UrlBuilder::RESOURCE_FOLLOW_BOARD);
        $this->assertFalse($this->provider->follow($boardId));
    }

    /** @test */
    public function it_should_unfollow_boards()
    {
        $boardId = 1;
        $this->setFollowSuccessResponse($boardId, UrlBuilder::RESOURCE_UNFOLLOW_BOARD);
        $this->assertTrue($this->provider->unFollow($boardId));

        $this->setFollowErrorResponse($boardId, UrlBuilder::RESOURCE_UNFOLLOW_BOARD);
        $this->assertFalse($this->provider->unFollow($boardId));
    }

    /** @test */
    public function it_should_return_generator_for_boards_followers()
    {
        $response = $this->createPaginatedResponse();
        $this->setResponseExpectation($response);
        $this->setResourceResponseData([]);
        $this->setResourceResponseData([]);

        $boardId = 1;
        $followers = $this->provider->followers($boardId);
        $this->assertInstanceOf(\Generator::class, $followers);
        $this->assertCount(2, iterator_to_array($followers));

        $followers = $this->provider->followers($boardId);
        $this->assertEmpty(iterator_to_array($followers));
    }

    /** @test */
    public function it_should_return_boards_for_specific_user()
    {
        $boards = ['data' => 'boards'];
        $userName = 'user';
        $response = $this->createApiResponse($boards);

        $this->setResponseExpectation($response);
        $this->assertEquals($boards['data'], $this->provider->forUser($userName));

        $this->setResponseExpectation();
        $this->assertFalse($this->provider->forUser($userName));
    }

    /** @test */
    public function it_should_return_board_info()
    {
        $response = $this->createApiResponse(['data' => 'info']);

        $this->setResponseExpectation($response);
        $this->assertEquals('info', $this->provider->info('username', 'board'));
        
        $this->setResponseExpectation();
        $this->assertFalse($this->provider->info('username', 'board'));
    }

    /** @test */
    public function it_should_return_generator_with_pins_for_specific_board()
    {
        $response = $this->createPaginatedResponse();

        $this->setResponseExpectation($response);
        $this->setResourceResponseData([]);
        $this->setResourceResponseData([]);

        $boardId = 1;
        $pins = $this->provider->pins($boardId);
        $this->assertInstanceOf(\Generator::class, $pins);
        $this->assertCount(2, iterator_to_array($pins));

        $pins = $this->provider->pins($boardId);
        $this->assertEmpty(iterator_to_array($pins));
    }

    /** @test */
    public function it_should_delete_board()
    {
        $this->setSuccessResponse(); 
        $this->assertTrue($this->provider->delete(1111));

        $this->setErrorResponse();        
        $this->assertFalse($this->provider->delete(1111));
    }

    /** @test */
    public function it_should_create_board()
    {
        $this->setSuccessResponse();
        $this->assertTrue($this->provider->create('test', 'test'));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->create('test', 'test'));
    }

    /** @test */
    public function it_should_update_board()
    {
        $attributes = [
            'category'    => 'test',
            'description' => 'test'
        ];

        $boardId = 1;
        $this->setSuccessResponse();
        $this->assertTrue($this->provider->update($boardId, $attributes));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->update($boardId, $attributes));
    }
}

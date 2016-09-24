<?php

namespace seregazhuk\tests\Bot\Api;

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

        $this->apiShouldReturnPagination();

        $res = $this->provider->search('dogs', 2);
        $this->assertIsPaginatedResponse($res);
    }

    /** @test */
    public function it_should_follow_boards()
    {
        $boardId = 1;
        $this->apiShouldFollowTo($boardId, UrlBuilder::RESOURCE_FOLLOW_BOARD)
            ->assertTrue($this->provider->follow($boardId));

        $this->apiShouldNotFollow($boardId, UrlBuilder::RESOURCE_FOLLOW_BOARD)
            ->assertFalse($this->provider->follow($boardId));
    }

    /** @test */
    public function it_should_unfollow_boards()
    {
        $boardId = 1;
        $this->apiShouldFollowTo($boardId, UrlBuilder::RESOURCE_UNFOLLOW_BOARD)
            ->assertTrue($this->provider->unFollow($boardId));

        $this->apiShouldNotFollow($boardId, UrlBuilder::RESOURCE_UNFOLLOW_BOARD)
            ->assertFalse($this->provider->unFollow($boardId));
    }

    /** @test */
    public function it_should_return_generator_for_boards_followers()
    {
        $this->apiShouldReturnPagination()
            ->apiShouldReturnEmpty();

        $boardId = 1;
        $followers = $this->provider->followers($boardId);

        $this->assertIsPaginatedResponse($followers);
    }

    /** @test */
    public function it_should_return_boards_for_specific_user()
    {
        $boards = 'boards';
        $userName = 'user';

        $this->apiShouldReturnData($boards)
            ->assertEquals($boards, $this->provider->forUser($userName));

        $this->apiShouldReturnEmpty()
            ->assertFalse($this->provider->forUser($userName));
    }

    /** @test */
    public function it_should_return_board_info()
    {
        $boardInfo = 'info';
        $this->apiShouldReturnData($boardInfo)
            ->assertEquals($boardInfo, $this->provider->info('username', 'board'));

        $this->apiShouldReturnEmpty()
            ->assertFalse($this->provider->info('username', 'board'));
    }

    /** @test */
    public function it_should_return_generator_with_pins_for_specific_board()
    {
        $this->apiShouldReturnPagination()
            ->apiShouldReturnEmpty();

        $boardId = 1;
        $pins = $this->provider->pins($boardId);

        $this->assertIsPaginatedResponse($pins);
    }

    /** @test */
    public function it_should_delete_board()
    {
        $this->apiShouldReturnSuccess()
            ->assertTrue($this->provider->delete(1111));

        $this->apiShouldReturnError()
            ->assertFalse($this->provider->delete(1111));
    }

    /** @test */
    public function it_should_create_board()
    {
        $this->apiShouldReturnSuccess()
            ->assertTrue($this->provider->create('test', 'test'));

        $this->apiShouldReturnError()
            ->assertFalse($this->provider->create('test', 'test'));
    }

    /** @test */
    public function it_should_update_board()
    {
        $attributes = [
            'category'    => 'test',
            'description' => 'test'
        ];

        $boardId = 1;
        $this->apiShouldReturnSuccess()
            ->assertTrue($this->provider->update($boardId, $attributes));

        $this->apiShouldReturnError()
            ->assertFalse($this->provider->update($boardId, $attributes));
    }
}

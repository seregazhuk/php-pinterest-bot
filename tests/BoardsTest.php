<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Api\Providers\Boards;

class BoardsTest extends ProviderTest
{
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
        $this->mock->shouldReceive('exec')->twice()->andReturn($response);

        $res = iterator_to_array($this->provider->search('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res[0]);
    }

    /** @test */
    public function followBoard()
    {
        $response = $this->createSuccessApiResponse();
        $error = $this->createErrorApiResponse();

        $this->mock->shouldReceive('followMethodCall')->once()->andReturn($response);
        $this->mock->shouldReceive('followMethodCall')->once()->andReturn($error);

        $this->assertTrue($this->provider->follow(1));
        $this->assertFalse($this->provider->follow(1));
    }

    /** @test */
    public function unFollowBoard()
    {
        $response = $this->createSuccessApiResponse();
        $error = $this->createErrorApiResponse();

        $this->mock->shouldReceive('followMethodCall')->once()->andReturn($response);
        $this->mock->shouldReceive('followMethodCall')->once()->andReturn($error);

        $this->assertTrue($this->provider->unFollow(1));
        $this->assertFalse($this->provider->unFollow(1));
    }

    /** @test */
    public function getBoardFollowers()
    {
        $response = $this->createPaginatedResponse();
        $this->mock->shouldReceive('exec')->once()->andReturn($response);

        $this->mock->shouldReceive('exec')->once()->andReturn(['resource_response' => ['data' => []]]);

        $this->mock->shouldReceive('exec')->once()->andReturn(
            [
                'resource_response' => [
                    'data' => [
                        ['type' => 'module'],
                    ],
                ],
            ]
        );

        $followers = $this->provider->followers(111);
        $this->assertCount(2, iterator_to_array($followers)[0]);
        $followers = $this->provider->followers(111);
        $this->assertEmpty(iterator_to_array($followers));
    }

    /** @test */
    public function getBoardsForSpecifiedUser()
    {
        $boards = ['data' => 'boards'];
        $response = $this->createApiResponse($boards);
        $this->mock->shouldReceive('exec')->once()->andReturn($response);
        $this->mock->shouldReceive('exec')->once()->andReturnNull();

        $this->assertEquals($boards['data'], $this->provider->forUser(1));
        $this->assertFalse($this->provider->forUser(1));
    }

    /** @test */
    public function getBoardInfo()
    {
        $response = $this->createApiResponse(['data' => 'info']);
        $this->mock->shouldReceive('exec')->once()->andReturn($response);
        $this->mock->shouldReceive('exec')->once()->andReturnNull();

        $this->assertEquals('info', $this->provider->info('username', 'board'));
        $this->assertFalse($this->provider->info('username', 'board'));
    }

    /** @test */
    public function getPinsFromBoard()
    {
        $response = $this->createPaginatedResponse();
        $this->mock->shouldReceive('exec')->once()->andReturn($response);

        $this->mock->shouldReceive('exec')->once()->andReturn(['resource_response' => ['data' => []]]);

        $this->mock->shouldReceive('exec')->once()->andReturn(
                [
                'resource_response' => [
                    'data' => [
                        ['type' => 'module'],
                    ],
                ],
            ]);

        $pins = $this->provider->pins(1);
        $this->assertCount(2, iterator_to_array($pins)[0]);
        $pins = $this->provider->pins(0);
        $this->assertEmpty(iterator_to_array($pins));
    }

    /** @test */
    public function deleteBoard()
    {
        $response = $this->createSuccessApiResponse();
        $error = $this->createErrorApiResponse();

        $this->mock->shouldReceive('exec')->once()->andReturn($response);
        $this->mock->shouldReceive('exec')->once()->andReturn($error);

        $this->assertTrue($this->provider->delete(1111));
        $this->assertFalse($this->provider->delete(1111));
    }

    /** @test */
    public function createBoard()
    {
        $response = $this->createSuccessApiResponse();
        $error = $this->createErrorApiResponse();

        $this->mock->shouldReceive('exec')->once()->andReturn($response);
        $this->mock->shouldReceive('exec')->once()->andReturn($error);

        $this->assertTrue($this->provider->create('test', 'test'));
        $this->assertFalse($this->provider->delete('test', 'test'));
    }
}
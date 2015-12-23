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

    public function testSearch()
    {
        $response['module']['tree']['data']['results'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $expectedResultsNum = count($response['module']['tree']['data']['results']);
        $this->mock->method('exec')->willReturn($response);

        $res = iterator_to_array($this->provider->search('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res[0]);
    }

    public function testFollow()
    {
        $response = $this->createSuccessApiResponse();
        $error = $this->createErrorApiResponse();

        $this->mock->expects($this->at(1))->method('exec')->willReturn($response);
        $this->mock->expects($this->at(3))->method('exec')->willReturn($error);

        $this->assertTrue($this->provider->follow(1));
        $this->assertFalse($this->provider->follow(1));
    }

    public function testUnFollow()
    {
        $response = $this->createSuccessApiResponse();
        $error = $this->createErrorApiResponse();

        $this->mock->expects($this->at(1))->method('exec')->willReturn($response);
        $this->mock->expects($this->at(3))->method('exec')->willReturn($error);

        $this->assertTrue($this->provider->unFollow(1));
        $this->assertFalse($this->provider->unFollow(1));
    }

    public function testGetBoardForUser()
    {
        $boards = ['data' => 'boards'];
        $response = $this->createApiResponse($boards);
        $this->mock->expects($this->at(0))->method('exec')->willReturn($response);

        $this->assertEquals($boards['data'], $this->provider->forUser(1));
        $this->assertFalse($this->provider->forUser(1));
    }

    public function testGetBoardInfo()
    {
        $response = $this->createApiResponse(['data' => 'info']);
        $this->mock->expects($this->at(0))->method('exec')->willReturn($response);

        $this->assertEquals('info', $this->provider->info('username', 'board'));
        $this->assertFalse($this->provider->info('username', 'board'));
    }

    public function testGetPins()
    {
        $response = $this->createPaginatedResponse();
        $this->mock->expects($this->at(0))
            ->method('exec')
            ->willReturn($response);

        $this->mock->expects($this->at(1))
            ->method('exec')
            ->willReturn(['resource_response' => ['data' => []]]);

        $this->mock->expects($this->at(2))
            ->method('exec')
            ->willReturn([
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

    public function delete()
    {
        $response = $this->createApiResponse();
        $error = $this->createErrorApiResponse();

        $this->mock->expects($this->at(1))->method('exec')->willReturn($response);
        $this->mock->expects($this->at(3))->method('exec')->willReturn($error);

        $this->assertTrue($this->provider->delete(1111));
        $this->assertFalse($this->provider->delete(1111));
    }

    public function create()
    {
        $response = $this->createApiResponse();
        $error = $this->createErrorApiResponse();

        $this->mock->expects($this->at(1))->method('exec')->willReturn($response);
        $this->mock->expects($this->at(3))->method('exec')->willReturn($error);

        $this->assertTrue($this->provider->create('test', 'test'));
        $this->assertFalse($this->provider->delete('test', 'test'));
    }
}
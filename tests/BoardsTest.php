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
        $this->setProperty('request', $this->mock);
        $res = iterator_to_array($this->provider->search('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res[0]);
    }

    public function testFollow()
    {
        $response = $this->createSuccessApiResponse();
        $error = $this->createErrorApiResponse();

        $this->mock->expects($this->at(1))->method('exec')->willReturn($response);
        $this->mock->expects($this->at(3))->method('exec')->willReturn($error);
        $this->setProperty('request', $this->mock);

        $this->assertTrue($this->provider->follow(1));
        $this->assertFalse($this->provider->follow(1));
    }

    public function testUnFollow()
    {
        $response = $this->createSuccessApiResponse();
        $error = $this->createErrorApiResponse();

        $this->mock->expects($this->at(1))->method('exec')->willReturn($response);
        $this->mock->expects($this->at(3))->method('exec')->willReturn($error);
        $this->setProperty('request', $this->mock);

        $this->assertTrue($this->provider->unFollow(1));
        $this->assertFalse($this->provider->unFollow(1));
    }

    //public function testMy()
    //{
    //    $initBoards                                     = ['first', 'second'];
    //    $res['resource_response']['data']['all_boards'] = $initBoards;
    //    $this->mock->method('exec')->willReturn($res);
    //    $this->setProperty('request', $this->mock);
    //    $boards = $this->provider->my();
    //    $this->assertEquals($initBoards, $boards);
    //    $res = null;
    //
    //    $this->mock = $this->createRequestMock();
    //    $this->mock->method('exec')->willReturn(json_encode($res));
    //    $this->setProperty('request', $this->mock);
    //    $boards = $this->provider->my();
    //    $this->assertFalse($boards);
    //}

}
<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Providers\Pins;

class PinsTest extends ProviderTest
{

    /**
     * @var Pins
     */
    protected $provider;


    protected function setUp()
    {
        $this->provider = new Pins($this->createRequestMock());
        parent::setUp();
    }

    public function testLikeAndUnlike()
    {
        $res['resource_response'] = [];
        $mock                     = $this->createRequestMock();
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->expects($this->at(3))->method('exec')->willReturn($res);
        $this->setProperty('request', $mock);
        $this->assertTrue($this->provider->like(1111));
        $this->assertTrue($this->provider->unLike(1111));
        $this->assertFalse($this->provider->like(1111));
        $this->assertFalse($this->provider->unLike(1111));
    }

    public function testComment()
    {
        $res['resource_response'] = [];
        $mock                     = $this->createRequestMock();
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->expects($this->at(5))->method('exec')->willReturn($res);
        $this->setProperty('request', $mock);
        $this->assertTrue($this->provider->comment(1111, 'comment text'));
        $this->assertFalse($this->provider->comment(1111, 'comment text'));

        $this->assertTrue($this->provider->deleteComment(1111, 1111));
        $this->assertFalse($this->provider->deleteComment(1111, 1111));
    }


    public function testPin()
    {
        $res['resource_response']['data']['id'] = 1;
        $mock                                   = $this->createRequestMock();
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $this->setProperty('request', $mock);

        $pinSource      = 'http://example.com/image.jpg';
        $pinDescription = 'Pin Description';
        $boardId        = 1;
        $this->assertNotFalse($this->provider->create($pinSource, $boardId, $pinDescription));
        $this->assertFalse($this->provider->create($pinSource, $boardId, $pinDescription));
    }

    public function testRepin()
    {
        $res['resource_response']['data']['id'] = 1;
        $mock                                   = $this->createRequestMock();
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $this->setProperty('request', $mock);

        $repinId        = 11;
        $pinDescription = 'Pin Description';
        $boardId        = 1;

        $this->assertNotFalse($this->provider->repin($repinId, $boardId, $pinDescription));
        $this->assertFalse($this->provider->repin($repinId, $boardId, $pinDescription));
    }

    public function testDeletePin()
    {
        $res['resource_response']['data'] = [];
        $mock                             = $this->createRequestMock();
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $this->setProperty('request', $mock);
        $this->assertNotFalse($this->provider->delete(1));
        $this->assertFalse($this->provider->delete(1));
    }

    public function testGetPinInfo()
    {
        $res['resource_response']['data'] = ['data'];
        $mock                             = $this->createRequestMock();
        $mock->expects($this->at(0))->method('exec')->willReturn($res);
        $mock->expects($this->at(1))->method('exec')->willReturn(['resource_response' => []]);
        $this->setProperty('request', $mock);
        $this->assertNotNull($this->provider->info(1));
        $this->assertNull($this->provider->info(1));
    }

    public function testSearch()
    {

        $response['module']['tree']['data']['results'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $expectedResultsNum = count($response['module']['tree']['data']['results']);
        $mock               = $this->createRequestMock();

        $mock->method('exec')->willReturn($response);
        $this->setProperty('request', $mock);
        $res = iterator_to_array($this->provider->search('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res[0]);
    }
}
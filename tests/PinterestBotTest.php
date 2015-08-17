<?php
use szhuk\PinterestAPI\PinterestBot;
use szhuk\PinterestAPI\ApiRequest;

class PinterestBotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PinterestBot;
     */
    protected $bot;

    /**
     * @var Mockable
     */
    protected $mock;

    /**
     * @var ReflectionClass
     */
    protected $reflection;

    protected function setUp()
    {
        $this->bot        = new PinterestBot('test', 'test', new ApiRequest());
        $this->reflection = new \ReflectionClass($this->bot);
    }

    protected function tearDown()
    {
        $this->bot        = null;
        $this->reflection = null;
    }

    public function getProperty($property)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($this->bot);
    }

    /**
     * Call protected methods of PinterestBot class
     *
     * @param string $name
     * @param array  $args
     * @return mixed
     */
    public function invokeMethod($name, $args)
    {
        $method = $this->reflection->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($this->bot, $args);
    }

    public function setProperty($property, $value)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        $property->setValue($this->bot, $value);
    }


    public function testLogin()
    {
        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(0))->method('exec')->willReturn([]);
        $mock->expects($this->at(1))->method('exec')->willReturn(null);
        $this->setProperty('api', $mock);
        $this->assertTrue($this->bot->login());
        $this->assertFalse($this->bot->login());
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage You must set username and password to login.
     */
    public function testLoginWithoutUsernameOrPassword()
    {
        $this->setProperty('username', null);
        $this->setProperty('password', null);
        $this->bot->login();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage You must log in before.
     */
    public function testCheckIsLoggedThrowsException()
    {
        $this->bot->checkLoggedIn();
    }


    public function testGetBoards()
    {
        $initBoards                                     = ['first', 'second'];
        $res['resource_response']['data']['all_boards'] = $initBoards;

        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);
        $boards = $this->bot->getBoards();
        $this->assertEquals($initBoards, $boards);

        $res  = null;
        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);
        $boards = $this->bot->getBoards();
        $this->assertNull($boards);
    }

    public function testGetAccountName()
    {
        $accountName                                                      = 'test';
        $res['resource_data_cache'][1]['resource']['options']['username'] = $accountName;
        $mock                                                             = $this->getMock(ApiRequest::class,
            ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $this->assertEquals($accountName, $this->bot->getAccountName());
        $this->assertNull($this->bot->getAccountName());
    }

    public function testFollowAndUnfollowUser()
    {
        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn([]);
        $mock->expects($this->at(3))->method('exec')->willReturn([]);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $this->assertTrue($this->bot->followUser(1111));
        $this->assertTrue($this->bot->unFollowUser(1111));

        $this->assertFalse($this->bot->followUser(1111));
        $this->assertFalse($this->bot->unFollowUser(1111));

    }

    public function testLikeAndUnlikePin()
    {
        $res['resource_response'] = [];
        $mock                     = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->expects($this->at(3))->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $this->assertTrue($this->bot->likePin(1111));
        $this->assertTrue($this->bot->unLikePin(1111));

        $this->assertFalse($this->bot->likePin(1111));
        $this->assertFalse($this->bot->unLikePin(1111));
    }

    public function testCommentPin()
    {
        $res['resource_response'] = [];
        $mock                     = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $this->assertTrue($this->bot->commentPin(1111, 'comment text'));
        $this->assertFalse($this->bot->commentPin(1111, 'comment text'));
    }

    public function testGetUserData()
    {
        $expected                                = ['data' => ['info' => ''], 'bookmarks' => ['booksmarks_string']];
        $res['resource']['options']['bookmarks'] = $expected['bookmarks'];
        $res['resource_response']['data']        = $expected['data'];

        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->expects($this->at(3))->method('exec')->willReturn(['resource_response' => []]);

        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $data = $this->bot->getUserData('test_user', 'http://example.com', 'http://example.com');
        $this->assertEquals($expected, $data);

        $data = $this->bot->getUserData('test_user', 'http://example.com', 'http://example.com', 'my_bookmarks');
        $this->assertEquals([], $data);
    }

    public function testGetUserInfo()
    {
        $res['resource_response'] = ['data' => ['name' => 'test']];
        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $data = $this->bot->getUserInfo($this->bot->username);
        $this->assertEquals($res['resource_response']['data'], $data);
    }

    public function testPin()
    {
        $res['resource_response']['data']['id'] = 1;
        $mock                                   = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $pinSource      = 'http://example.com/image.jpg';
        $pinDescription = 'Pin Description';
        $boardId        = 1;
        $this->assertNotFalse($this->bot->pin($pinSource, $boardId, $pinDescription));
        $this->assertFalse($this->bot->pin($pinSource, $boardId, $pinDescription));
    }

    public function testRepin()
    {
        $res['resource_response']['data']['id'] = 1;
        $mock                                   = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $repinId        = 11;
        $pinDescription = 'Pin Description';
        $boardId        = 1;
        $this->assertNotFalse($this->bot->repin($repinId, $boardId, $pinDescription));
        $this->assertFalse($this->bot->repin($repinId, $boardId, $pinDescription));
    }

    public function testDeletePin()
    {
        $res['resource_response']['data'] = [];
        $mock                             = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $this->assertNotFalse($this->bot->deletePin(1));
        $this->assertFalse($this->bot->deletePin(1));
    }


    public function testGetFollowersAndFollowing()
    {
        $res['resource_response']['data'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))
            ->method('exec')
            ->willReturn($res);

        $mock->expects($this->at(2))
            ->method('exec')
            ->willReturn(['resource_response' => ['data' => []]]);

        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $followers = $this->bot->getFollowers($this->bot->username);
        $this->assertCount(2, iterator_to_array($followers)[0]);
    }

    public function testGetFollowing()
    {
        $res['resource_response']['data'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))
            ->method('exec')
            ->willReturn($res);

        $mock->expects($this->at(2))
            ->method('exec')
            ->willReturn(['resource_response' => ['data' => []]]);

        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $following = $this->bot->getFollowing($this->bot->username);
        $this->assertCount(2, iterator_to_array($following)[0]);
    }

    public function testPinnerPins()
    {
        $res['resource_response']['data'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))
            ->method('exec')
            ->willReturn($res);
        $mock->expects($this->at(2))
            ->method('exec')
            ->willReturn(['resource_response' => ['data' => []]]);

        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $pins = $this->bot->getUserPins($this->bot->username);
        $expectedResultsNum = count($res['resource_response']['data']);
        $this->assertCount($expectedResultsNum, iterator_to_array($pins)[0]);
    }


    public function testSearch()
    {
        $response = [
            'resource_response' => [
                'data' => [
                    ['id' => 1],
                    ['id' => 2],
                ],
            ],
            'resource'          => [
                'options' => ['bookmarks' => 'my_bookmarks'],
            ],
        ];

        $response_with_bookmarks = [
            'resource_response' => [
                'data' => [
                    ['id' => 1],
                    ['id' => 2],
                ],
            ],
            'resource'          => [
                'options' => ['bookmarks' => 'my_bookmarks'],
            ],
        ];

        $expected = [
            'data'      => $response_with_bookmarks['resource_response']['data'],
            'bookmarks' => $response_with_bookmarks['resource']['options']['bookmarks'],
        ];

        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->method('exec')->willReturn($response_with_bookmarks);

        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $res = $this->bot->search('cats', PinterestBot::SEARCH_PINS_SCOPES, 'bookmarks');
        $this->assertEquals($expected, $res);
    }


    public function testSearchFunctions()
    {
        $response['module']['tree']['data']['results'] = [
            ['id' => 1],
            ['id' => 2],
        ];
        $expectedResultsNum                            = count($response['module']['tree']['data']['results']);

        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->method('exec')->willReturn($response);
        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $res = iterator_to_array($this->bot->searchPins('dogs'));
        $this->assertCount($expectedResultsNum, $res[0]);

        $res = iterator_to_array($this->bot->searchPinners('dogs'));
        $this->assertCount($expectedResultsNum, $res[0]);

        $res = iterator_to_array($this->bot->searchBoards('dogs'));
        $this->assertCount($expectedResultsNum, $res[0]);
    }


    public function testCheckErrorInResponse()
    {
        $response = [
            [
                'api_error_code' => 404,
                'message'        => 'Not found',
            ],
        ];
        $this->invokeMethod('checkErrorInResponse', $response);
        $this->assertEquals($response[0]['api_error_code'], $this->bot->lastApiErrorCode);
        $this->assertEquals($response[0]['message'], $this->bot->lastApiErrorMsg);

        $this->invokeMethod('checkErrorInResponse', [[]]);
        $this->assertNull($this->bot->lastApiErrorCode);
        $this->assertNull($this->bot->lastApiErrorMsg);
    }
}

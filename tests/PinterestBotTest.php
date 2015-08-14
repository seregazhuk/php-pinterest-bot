<?php
use Pinterest\PinterestBot;
use Pinterest\ApiRequest;

/**
 * Created by PhpStorm.
 * User: serega
 * Date: 31.07.15
 * Time: 23:42
 */
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
		$this->bot = new PinterestBot('test', 'test', new ApiRequest());
		$this->reflection = new \ReflectionClass($this->bot);
	}

	protected function tearDown()
	{
		$this->bot = null;
		$this->reflection = null;
	}

	public function getProperty($property)
	{
		$property = $this->reflection->getProperty($property);
		$property->setAccessible(true);
		return $property->getValue($this->bot);
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
		$mock->method('exec')->willReturn([]);

		$this->setProperty('api', $mock);
		$res = $this->bot->login();
		$this->assertTrue($res);
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
		$initBoards = ['first', 'second'];
		$res['resource_response']['data']['all_boards'] = $initBoards;

		$mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
		$mock->method('exec')->willReturn($res);
		$mock->method('isLoggedIn')->willReturn(true);

		$this->setProperty('api', $mock);
		$boards = $this->bot->getBoards();
		$this->assertEquals($initBoards, $boards);

		$res = null;
		$mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
		$mock->method('exec')->willReturn($res);
		$mock->method('isLoggedIn')->willReturn(true);

		$this->setProperty('api', $mock);
		$boards = $this->bot->getBoards();
		$this->assertNull($boards);
	}

	public function testGetAccountName()
	{
		$accountName = 'test';
		$res['resource_data_cache'][1]['resource']['options']['username'] = $accountName;
		$mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
		$mock->method('exec')->willReturn($res);
		$mock->method('isLoggedIn')->willReturn(true);

		$this->setProperty('api', $mock);
		$this->assertEquals($accountName, $this->bot->getAccountName());
	}

	public function testFollowAndUnfollowUser()
	{

		$mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
		$mock->method('exec')->willReturn([]);
		$mock->method('isLoggedIn')->willReturn(true);

		$this->setProperty('api', $mock);

		$this->assertTrue($this->bot->followUser(1111));
		$this->assertTrue($this->bot->unFollowUser(1111));

	}

	public function testLikeAndUnlikePin()
	{
		$res['resource_response'] = [];
		$mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
		$mock->method('exec')->willReturn($res);
		$mock->method('isLoggedIn')->willReturn(true);

		$this->setProperty('api', $mock);

		$this->assertTrue($this->bot->likePin(1111));
		$this->assertTrue($this->bot->unLikePin(1111));

	}

	public function testCommentPin()
	{
		$res['resource_response'] = [];
		$mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
		$mock->method('exec')->willReturn($res);
		$mock->method('isLoggedIn')->willReturn(true);

		$this->setProperty('api', $mock);

		$this->assertTrue($this->bot->commentPin(1111, 'comment text'));
	}

	public function testGetUserData()
	{
		$expected = ['data'=>['data'=>''], 'bookmarks' => ['booksmarks_string']];
		$res['resource']['options']['bookmarks'] = $expected['bookmarks'];
		$res['resource_response']['data'] = [$expected['data']];

		$mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
		$mock->method('exec')->willReturn($res);
		$mock->method('isLoggedIn')->willReturn(true);

		$this->setProperty('api', $mock);

		$data = $this->bot->getUserData('test_user', 'http://example.com', 'http://example.com');
		$this->assertEquals($expected, $data);
	}

	public function testGetUserInfo()
	{
		$res = [];
		$mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
		$mock->method('exec')->willReturn($res);
		$mock->method('isLoggedIn')->willReturn(true);

		$this->setProperty('api', $mock);

		$data = $this->bot->getUserInfo($this->bot->username);
		$this->assertEquals($data, $res);
	}

	public function testPin()
	{
		$res['resource_response']['data']['id'] = 1;
		$mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
		$mock->method('exec')->willReturn($res);
		$mock->method('isLoggedIn')->willReturn(true);

		$this->setProperty('api', $mock);

		$pinSource = 'http://example.com/image.jpg';
		$pinDescription = 'Pin Description';
		$boardId = 1;
		$this->assertNotFalse($this->bot->pin($pinSource, $boardId, $pinDescription));
	}

	public function testRepin()
	{
		$res['resource_response']['data']['id'] = 1;
		$mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
		$mock->method('exec')->willReturn($res);
		$mock->method('isLoggedIn')->willReturn(true);

		$this->setProperty('api', $mock);

		$repinId = 11;
		$pinDescription = 'Pin Description';
		$boardId = 1;
		$this->assertNotFalse($this->bot->repin($repinId, $boardId, $pinDescription));
	}

	public function testDeletePin()
	{
		$res['resource_response']['data'] = [];
		$mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
		$mock->method('exec')->willReturn($res);
		$mock->method('isLoggedIn')->willReturn(true);
		$this->setProperty('api', $mock);

		$this->assertNotFalse($this->bot->deletePin(1));
	}

}

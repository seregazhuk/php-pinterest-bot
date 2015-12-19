<?php

namespace szhuk\tests;

use seregazhuk\PinterestBot\Request;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Http;
use seregazhuk\tests\helpers\ReflectionHelper;

class RequestTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelper;

    /**
     * @var Request;
     */
    protected $request;

    /**
     * Mock
     */
    public $mock;

    protected function setUp()
    {
        $this->request = new Request(new Http());
        $this->reflection = new \ReflectionClass($this->request);
        $this->setReflectedObject($this->request);
    }

    protected function tearDown()
    {
        $this->request = null;
        $this->mock = null;
        $this->reflection = null;
    }

    public function testCheckErrorInResponse()
    {
        $response = [
            'api_error_code' => 404,
            'message'        => 'Not found',
        ];

        $this->request->checkErrorInResponse($response);
        $this->assertEquals($response['api_error_code'], $this->request->lastApiErrorCode);
        $this->assertEquals($response['message'], $this->request->lastApiErrorMsg);
    }

    /**
     * @expectedException \LogicException
     */
    public function testLogIn()
    {
        $this->request->setLoggedIn();
        $this->assertTrue($this->request->checkLoggedIn());
        $token = $this->getProperty('csrfToken');
        $this->assertNotEquals(Request::DEFAULT_CSRFTOKEN, $token);
        $this->assertTrue($this->request->isLoggedIn());

        $this->request->clearToken();
        $token = $this->getProperty('csrfToken');
        $this->assertEquals(Request::DEFAULT_CSRFTOKEN, $token);

        $this->setProperty('loggedIn', false);
        $this->request->checkLoggedIn();
    }

    public function testExec()
    {
        $httpMock = $this->getMock(Http::class, ['setOptions', 'execute', 'close']);
        $response = $this->createSuccessResponse();
        $httpMock->method('execute')->willReturn(json_encode($response));
        $this->setProperty('http', $httpMock);
        $res = $this->request->exec('http://example.com', 'a=b');
        $this->assertEquals($response, $res);

        $this->request->clearToken();
        $res = $this->request->exec('http://example.com', 'a=b');
        $this->assertEquals($response, $res);
    }

    public function testSearchWithoutBookmarks()
    {
        $response = [
            'module'   => [
                'tree' => [
                    'data'     => [
                        'results' => [
                            'my_first_result',
                        ],
                    ],
                    'resource' => [
                        'options' => [
                            'bookmarks' => ['my_bookmarks'],
                        ],
                    ],
                ],
            ],
            'resource' => [
                'options' => ['bookmarks' => 'my_bookmarks'],
            ],
        ];
        $expected = [
            'data'      => $response['module']['tree']['data']['results'],
            'bookmarks' => $response['module']['tree']['resource']['options']['bookmarks'],
        ];
        $mock = $this->getMock(Http::class, ['execute']);
        $mock->method('execute')->willReturn(json_encode($response));
        $response['module']['tree']['data']['results'] = [];
        $this->setProperty('http', $mock);
        $res = $this->request->searchCall('cats', Request::SEARCH_PINS_SCOPE, []);
        $this->assertEquals($expected, $res);
    }

    public function testFollowMethodCall()
    {
        $response = $this->createSuccessResponse();
        $mock = $this->getMock(Http::class, ['setOptions', 'execute', 'close']);
        $mock->expects($this->at(1))->method('execute')->willReturn(json_encode($response));
        $mock->expects($this->at(2))->method('execute')->willReturn(null);

        $this->setProperty('http', $mock);
        $this->assertTrue($this->request->followMethodCall(1, Request::BOARD_ENTITY_ID, 'ur'));
        $this->assertFalse($this->request->followMethodCall(1, Request::INTEREST_ENTITY_ID, 'ur'));
    }

    /**
     * @return array
     */
    protected function createSuccessResponse()
    {
        return ['body' => 'result'];
    }
}

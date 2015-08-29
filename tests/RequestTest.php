<?php

namespace szhuk\tests;

use seregazhuk\PinterestBot\Request;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Http;

class RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Request;
     */
    protected $Request;


    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * Mock
     */
    public $mock;


    protected function setUp()
    {
        $this->Request    = new Request(new Http());
        $this->reflection = new \ReflectionClass($this->Request);
    }

    public function getProperty($property)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($this->Request);
    }


    public function setProperty($property, $value)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        $property->setValue($this->Request, $value);
    }

    protected function tearDown()
    {
        $this->Request    = null;
        $this->mock       = null;
        $this->reflection = null;
    }

    public function testCheckErrorInResponse()
    {
        $response = [
            'api_error_code' => 404,
            'message'        => 'Not found',
        ];

        $this->Request->checkErrorInResponse($response);
        $this->assertEquals($response['api_error_code'], $this->Request->lastApiErrorCode);
        $this->assertEquals($response['message'], $this->Request->lastApiErrorMsg);
    }

    /**
     * @expectedException \LogicException
     */
    public function testLogIn()
    {
        $this->Request->setLoggedIn();
        $this->assertTrue($this->Request->checkLoggedIn());
        $token = $this->getProperty('csrfToken');
        $this->assertNotEquals(Request::DEFAULT_CSRFTOKEN, $token);

        $this->Request->clearToken();
        $token = $this->getProperty('csrfToken');
        $this->assertEquals(Request::DEFAULT_CSRFTOKEN, $token);

        $this->setProperty('loggedIn', false);
        $this->Request->checkLoggedIn();
    }

    public function testExec()
    {
        $httpMock = $this->getMock(Http::class, ['setOptions', 'execute', 'close']);
        $response = ['body' => 'text'];
        $httpMock->method('execute')->willReturn(json_encode($response));
        $this->setProperty('http', $httpMock);
        $res = $this->Request->exec('http://example.com', 'a=b');
        $this->assertEquals($response, $res);

        $this->Request->clearToken();
        $res = $this->Request->exec('http://example.com', 'a=b');
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
        $mock     = $this->getMock(Http::class, ['execute']);
        $mock->method('execute')->willReturn(json_encode($response));
        $response['module']['tree']['data']['results'] = [];
        $this->setProperty('http', $mock);
        $res = $this->Request->_search('cats', Request::SEARCH_PINS_SCOPE, []);
        $this->assertEquals($expected, $res);
    }

    public function testFollowMethodCall()
    {
        $response = ['body' => 'result'];
        $mock     = $this->getMock(Http::class, ['setOptions', 'execute', 'close']);
        $mock->expects($this->at(1))->method('execute')->willReturn(json_encode($response));
        $mock->expects($this->at(2))->method('execute')->willReturn(null);

        $this->setProperty('http', $mock);
        $this->assertTrue($this->Request->followMethodCall(1, Request::BOARD_ENTITY_ID, 'ur'));
        $this->assertFalse($this->Request->followMethodCall(1, Request::INTEREST_ENTITY_ID, 'ur'));

    }
}

<?php

namespace szhuk\tests;

use Mockery;
use ReflectionClass;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\CurlAdapter;
use seregazhuk\tests\helpers\ResponseHelper;
use seregazhuk\tests\helpers\ReflectionHelper;
use seregazhuk\PinterestBot\Helpers\CsrfHelper;
use seregazhuk\PinterestBot\Contracts\HttpInterface;

/**
 * Class RequestTest.
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelper, ResponseHelper;

    /** @test */
    public function checkLoggedInFailure()
    {
        $request = $this->createRequestObject();
        $this->setProperty('loggedIn', false);
        $this->assertFalse($request->isLoggedIn(), 'Failed asserting logged in property');
    }

    /** @test */
    public function checkLoggedInSuccess()
    {
        $request = $this->createRequestObject();
        $this->setProperty('loggedIn', true);
        $this->assertTrue($request->isLoggedIn(), 'Failed asserting logged in property');
    }

    /** @test */
    public function executeRequestToPinterestApi()
    {
        $response = $this->createSuccessApiResponse();
        $http = $this->getHttpMock();
        $http->shouldReceive('execute')->once()->andReturn(json_encode($response));

        $http->shouldReceive('execute')->once()->andReturnNull();
        $request = $this->createRequestObject($http);

        $res = $request->exec('endpoint', 'a=b');
        $this->assertEquals($response, $res);

        $res = $request->exec('endpoint', 'a=b');
        $this->assertNull($res);
    }

    /** @test */
    public function executeFollowRequestToPinterestApi()
    {
        $response = $this->createSuccessApiResponse();
        $http = $this->getHttpMock();
        $http->shouldReceive('execute')->once()->andReturn(json_encode($response));
        $http->shouldReceive('execute')->once()->andReturnNull();
        $request = $this->createRequestObject($http);

        $this->assertEquals($response, $request->followMethodCall(1, 'entity_id', 'ur'));
        $this->assertNull($request->followMethodCall(1, 'entity_id', 'ur'));
    }

    /** @test */
    public function setUserAgent()
    {
        $userAgentString = 'UserAgentString';

        $request = $this->createRequestObject(new CurlAdapter());
        $request->setUserAgent($userAgentString);
        $this->assertEquals($userAgentString, $this->getProperty('userAgent'));
    }

    /** @test */
    public function clearToken()
    {
        $request = $this->createRequestObject();
        $this->assertEmpty($this->getProperty('csrfToken'));

        $request->clearToken();
        $this->assertEquals(CsrfHelper::DEFAULT_TOKEN, $this->getProperty('csrfToken'));
    }

    /** @test */
    public function createEmptyRequest()
    {
        $emptyRequest = [
            'source_url' => '',
            'data'       => json_encode(
                [
                    'options' => [],
                    'context' => new \stdClass(),
                ]
            ),
        ];

        $object = $this->createRequestObject();
        $request = $object->createRequestData();
        $this->assertEquals($emptyRequest, $request);
        $this->assertEquals('', $request['source_url']);
    }

    /** @test */
    public function createRequestWithData()
    {
        $data = ['key' => 'val'];

        $object = $this->createRequestObject();
        $request = $object->createRequestData($data);

        $dataFromRequest = json_decode($request['data'], true);
        $this->assertEquals($data['key'], $dataFromRequest['key']);
    }

    /** @test */
    public function createRequestWithBookmarks()
    {
        $bookmarks = 'bookmarks';

        $object = $this->createRequestObject();
        $request = $object->createRequestData([], $bookmarks);
        $dataFromRequest = json_decode($request['data'], true);

        $this->assertEquals($bookmarks, $dataFromRequest['options']['bookmarks']);
    }

    /** @test */
    public function login()
    {
        $cookieFile = __DIR__.'/../'.Request::COOKIE_NAME;
        $token = 'WfdvEjNSLYiykJHDIx4sGSpCS8OhUld0';
        file_put_contents(
            $cookieFile, ".pinterest.com	TRUE	/	TRUE	1488295594	csrftoken	$token"
        );
        $request = $this->createRequestObject();
        $this->setProperty('cookieJar', $cookieFile);
        $request->login();

        $this->assertEquals($token, $this->getProperty('csrfToken'));
    }

    /** @test */
    public function logoutClearsTokenAndLoggedInStatus()
    {
        $request = $this->createRequestObject();
        $this->setProperty('loggedIn', true);

        $request->logout();
        $this->assertFalse($request->isLoggedIn());
        $this->assertEquals(CsrfHelper::DEFAULT_TOKEN, $this->getProperty('csrfToken'));
    }

    protected function tearDown()
    {
        Mockery::close();
    }
    
    /**
     * @return Mockery\Mock|HttpInterface
     */
    protected function getHttpMock()
    {
        $mock = Mockery::mock(HttpInterface::class);

        return $mock;
    }

    /**
     * @param HttpInterface $http
     * @param string $userAgentString
     *
     * @return Request
     */
    protected function createRequestObject(HttpInterface $http = null, $userAgentString = '')
    {
        if (!$http) {
            $http = new CurlAdapter();
        }
        $request = new Request($http, $userAgentString);

        $this->reflection = new ReflectionClass($request);
        $this->setReflectedObject($request);

        return $request;
    }
}

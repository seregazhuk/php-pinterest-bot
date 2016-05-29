<?php

namespace szhuk\tests;

use Mockery;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use seregazhuk\PinterestBot\Api\CurlAdapter;
use seregazhuk\PinterestBot\Api\Providers\Boards;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Contracts\HttpInterface;
use seregazhuk\PinterestBot\Helpers\CsrfHelper;
use seregazhuk\tests\helpers\ReflectionHelper;
use seregazhuk\tests\helpers\ResponseHelper;

/**
 * Class RequestTest.
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelper, ResponseHelper;

    /**
     * @param HttpInterface $http
     * @param string        $userAgentString
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

    protected function tearDown()
    {
        Mockery::close();
    }

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

    /**
     * @return Mockery\Mock|HttpInterface
     */
    protected function getHttpMock()
    {
        $mock = Mockery::mock(HttpInterface::class);

        return $mock;
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
        $this->assertEquals(CsrfHelper::DEFAULT_TOKEN, $request->csrfToken);
    }

    /** @test */
    public function createEmptyRequest()
    {
        $emptyRequest = [
            'source_url' => '/',
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
        $this->assertEquals('/', $request['source_url']);
    }

    /** @test */
    public function createRequestWithData()
    {
        $sourceUrl = 'http://example.com';
        $data = ['key' => 'val'];

        $object = $this->createRequestObject();
        $request = $object->createRequestData($data, $sourceUrl);

        $dataFromRequest = json_decode($request['data'], true);
        $this->assertEquals($sourceUrl, $request['source_url']);
        $this->assertEquals($data['key'], $dataFromRequest['key']);
    }

    /** @test */
    public function createRequestWithBookmarks()
    {
        $bookmarks = 'bookmarks';

        $object = $this->createRequestObject();
        $request = $object->createRequestData([], '/', $bookmarks);
        $dataFromRequest = json_decode($request['data'], true);

        $this->assertEquals($bookmarks, $dataFromRequest['options']['bookmarks']);
    }

    /** @test */
    public function setLoggedIn()
    {
        $cookieFile = __DIR__.'/../'.Request::COOKIE_NAME;
        $token = 'WfdvEjNSLYiykJHDIx4sGSpCS8OhUld0';
        file_put_contents(
            $cookieFile, ".pinterest.com	TRUE	/	TRUE	1488295594	csrftoken	$token"
        );
        $request = $this->createRequestObject();
        $this->setProperty('cookieJar', $cookieFile);
        $request->setLoggedIn();

        $this->assertEquals($token, $request->csrfToken);
    }
}

<?php

namespace seregazhuk\tests\Bot;

use Mockery;
use Mockery\Mock;
use ReflectionClass;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\tests\Helpers\CookiesHelper;
use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\tests\helpers\ResponseHelper;
use seregazhuk\tests\helpers\ReflectionHelper;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;

/**
 * Class RequestTest.
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelper, ResponseHelper, CookiesHelper;

    /** @test */
    public function it_should_return_logged_in_status()
    {
        $request = $this->createRequestObject();
        $this->setProperty('loggedIn', false);
        $this->assertFalse($request->isLoggedIn());

        $this->setProperty('loggedIn', true);
        $this->assertTrue($request->isLoggedIn());
    }

    /** @test */
    public function it_should_set_csrf_token_to_default_value_after_clear()
    {
        $request = $this->createRequestObject();
        $this->assertEmpty($this->getProperty('csrfToken'));

        $request->clearToken();
        $this->assertEquals(Cookies::DEFAULT_TOKEN, $this->getProperty('csrfToken'));
    }

    /** @test */
    public function it_should_create_simple_pinterest_request_object()
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
    public function it_should_create_pinterest_request_object_with_data()
    {
        $data = ['key' => 'val'];

        $object = $this->createRequestObject();
        $request = $object->createRequestData($data);

        $dataFromRequest = json_decode($request['data'], true);
        $this->assertEquals($data['key'], $dataFromRequest['key']);
    }

    /** @test */
    public function it_should_create_pinterest_request_object_with_bookmarks()
    {
        $bookmarks = 'bookmarks';

        $object = $this->createRequestObject();
        $request = $object->createRequestData([], $bookmarks);
        $dataFromRequest = json_decode($request['data'], true);

        $this->assertEquals($bookmarks, $dataFromRequest['options']['bookmarks']);
    }

    /** @test */
    public function it_should_clear_token_and_login_status_after_logout()
    {
        $request = $this->createRequestObject();
        $this->setProperty('loggedIn', true);

        $request->logout();
        $this->assertFalse($request->isLoggedIn());
        $this->assertEquals(Cookies::DEFAULT_TOKEN, $this->getProperty('csrfToken'));
    }

    /**
     * @test
     * @expectedException \seregazhuk\PinterestBot\Exceptions\InvalidRequest
     */
    public function it_should_throw_exception_uploading_file_that_does_not_exist()
    {
        $this->createRequestObject()->upload('image.jpg', 'http://uploadurl.com');
    }

    /** @test */
    public function it_should_load_cookies_from_previously_saved_session_on_auto_login()
    {
        $cookieFilePath = $this->getCookiePath('test');
        $this->createCookieFile($cookieFilePath);

        $request = $this->createRequestObject();
        $request->autoLogin('test');

        $this->assertNotEmpty($request->getHttpClient()->cookies());
        unlink($cookieFilePath);
    }

    /** @test */
    public function it_should_return_false_on_auto_login_when_auth_cookie_not_found()
    {
        $cookieFilePath = $this->getCookiePath('test');
        $this->createCookieFile($cookieFilePath, false);

        $request = $this->createRequestObject();
        $this->assertFalse($request->autoLogin('test'));
    }

    /** @test */
    public function it_should_return_true_on_auto_login_when_auth_cookie_exist()
    {
        $cookieFilePath = $this->getCookiePath('test');
        $this->createCookieFile($cookieFilePath);

        $request = $this->createRequestObject();

        $this->assertTrue($request->autoLogin('test'));
        $this->assertTrue($request->isLoggedIn());
    }

    /** @test */
    public function it_should_create_post_data_for_upload()
    {
        $http = $this->getHttpObject();
        $image = 'image.jpg';
        file_put_contents($image, '');

        $this->http_should_execute_and_return($http, json_encode([]));
        $request = $this->createRequestObject($http);

        $request->upload($image, 'http://uploadurl.com');
        $this->assertNotEmpty($this->getProperty('postFileData'));
        unlink($image);
    }

    /**
     * @param Mock $http
     * @param mixed $returnsValue
     * @param int $times
     */
    protected function http_should_execute_and_return($http, $returnsValue, $times = 1)
    {
        $http->shouldReceive('execute')
            ->times($times)
            ->andReturn($returnsValue);
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    /**
     * @return Mock|HttpClient
     */
    protected function getHttpObject()
    {
        $mock = Mockery::mock(HttpClient::class);

        return $mock;
    }

    /**
     * @param HttpClient $http
     * @param string $userAgentString
     *
     * @return Request
     */
    protected function createRequestObject(HttpClient $http = null, $userAgentString = '')
    {
        $http = $http ? : new CurlHttpClient(new Cookies());
        $request = new Request($http, $userAgentString);

        $this->reflection = new ReflectionClass($request);
        $this->setReflectedObject($request);

        return $request;
    }
}

<?php

namespace seregazhuk\tests\Bot;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\tests\Helpers\CookiesHelper;
use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\tests\Helpers\ResponseHelper;
use seregazhuk\tests\Helpers\ReflectionHelper;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;

/**
 * Class RequestTest.
 */
class RequestTest extends TestCase
{
    use ReflectionHelper, ResponseHelper, CookiesHelper;

    /** @test */
    public function it_should_create_pinterest_empty_request_data()
    {
        $emptyRequest = [
            'source_url' => '',
            'data'       => json_encode(
                [
                    'options' => new \stdClass(),
                    'context' => new \stdClass(),
                ]
            ),
        ];

        $request = $this->createRequestObject();
        $data = $request->createRequestData();

        $this->assertEquals($emptyRequest, $data);
    }

    /** @test */
    public function it_should_create_pinterest_request_object_with_bookmarks()
    {
        $bookmarks = ['bookmarks'];

        $object = $this->createRequestObject();
        $request = $object->createRequestData([], $bookmarks);
        $dataFromRequest = json_decode($request['data'], true);

        $this->assertEquals($bookmarks, $dataFromRequest['options']['bookmarks']);
    }

    /** @test */
    public function it_should_clear_token_and_login_status_after_logout()
    {
        $request = $this->createRequestObject();

        $request->logout();

        $this->assertFalse($request->isLoggedIn());
    }

    /**
     * @test
     * @expectedException \seregazhuk\PinterestBot\Exceptions\InvalidRequest
     */
    public function it_should_throw_exception_uploading_file_that_does_not_exist()
    {
        $this
            ->createRequestObject()
            ->upload('image.jpg', 'http://uploadurl.com');
    }

    /** @test */
    public function it_should_load_cookies_from_previously_saved_session_on_auto_login()
    {
        $this->createCookieFile();

        $request = $this->createRequestObject();
        $request->autoLogin('test');

        $cookies = $request->getHttpClient()->cookies();

        $this->assertNotEmpty($cookies);
        $this->assertArrayHasKey('csrftoken', $cookies);
        $this->assertTrue($request->isLoggedIn());
    }

    /** @test */
    public function it_should_not_login_on_auto_login_when_auth_cookie_not_found()
    {
        $this->createCookieFile(false);

        $request = $this->createRequestObject();

        $this->assertFalse($request->autoLogin('test'));
    }

    /** @test */
    public function it_should_login_on_auto_login_when_auth_cookie_exist()
    {
        $this->createCookieFile();

        $request = $this->createRequestObject();

        $this->assertTrue($request->autoLogin('test'));
        $this->assertTrue($request->isLoggedIn());
    }

    /** @test */
    public function it_should_create_post_data_for_upload()
    {
        $http = $this->getHttpObject([
            'cookie' => '',
            'execute' => json_encode([])
        ]);

        $image = 'image.jpg';
        file_put_contents($image, '');

        $request = $this->createRequestObject($http);

        $request->upload($image, 'http://uploadurl.com');
        $this->assertNotEmpty($this->getProperty('postFileData'));

        unlink($image);
    }

    /** @test */
    public function it_should_delegate_current_url_to_http_client()
    {
        $currentUrl = 'http://example.com';

        $http = $this->getHttpObject(['getCurrentUrl' => $currentUrl]);

        $request = $this->createRequestObject($http);

        $this->assertEquals($currentUrl, $request->getCurrentUrl());
    }

    /** @test */
    public function it_should_drop_cookies_and_clear_token_when_drop_cookies()
    {
        $request = $this->createRequestObject();

        $request->dropCookies();

        $this->assertFalse($request->isLoggedIn());
    }

    /** @test */
    public function it_can_be_checked_for_a_token()
    {
        $request = $this->createRequestObject();

        $this->setProperty('csrfToken', 'test-token');
        $this->assertTrue($request->hasToken());

        $this->setProperty('csrfToken', Request::DEFAULT_TOKEN);
        $this->assertFalse($request->hasToken());
    }

    /**
     * @param array $methods
     * @return MockInterface|HttpClient
     */
    protected function getHttpObject(array $methods = [])
    {
        return Mockery::mock(HttpClient::class, $methods);
    }

    /**
     * @param HttpClient $http
     *
     * @return Request
     */
    protected function createRequestObject(HttpClient $http = null)
    {
        $http = $http ? : new CurlHttpClient(new Cookies());
        $request = new Request($http);

        $this->setReflectedObject($request);

        return $request;
    }
}

<?php

namespace szhuk\tests;

use seregazhuk\PinterestBot\ApiRequest;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;

class ApiRequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ApiRequest;
     */
    protected $apiRequest;


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
        $this->apiRequest = new ApiRequest('test', 'test');
        $this->reflection = new \ReflectionClass($this->apiRequest);
    }

    public function getProperty($property)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($this->apiRequest);
    }


    public function setProperty($property, $value)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        $property->setValue($this->apiRequest, $value);
    }

    protected function tearDown()
    {
        $this->apiRequest = null;
        $this->mock       = null;
        $this->reflection = null;
    }

    public function testCommonCurlOptions()
    {
        $referer = 'http://google.com';
        $headers = [
            10 => 'TestHeader: test',
        ];

        $this->setProperty('ch', curl_init());

        $this->apiRequest->setCurlOptions($referer, "", $headers);
        $requestOptions = $this->getProperty('options');

        $this->assertArrayNotHasKey(CURLOPT_POST, $requestOptions);
        $this->assertArrayHasKey(CURLOPT_REFERER, $requestOptions);
        $this->assertArraySubset($headers, $requestOptions[CURLOPT_HTTPHEADER]);

        $postString = 'post';
        $this->apiRequest->setCurlOptions($referer, $postString, $headers, false);
        $requestOptions = $this->getProperty('options');
        $this->assertArrayHasKey(CURLOPT_POST, $requestOptions);

        $this->apiRequest->setCurlOptions($referer, $postString, $headers, true, false);
        $requestOptions = $this->getProperty('options');

        $this->assertArraySubset([9 => 'X-CSRFToken: '], $requestOptions[CURLOPT_HTTPHEADER]);
    }

    public function testLoggedIn()
    {
        $this->apiRequest->setLoggedIn('token');
        $this->assertEquals(true, $this->getProperty('loggedIn'));
        $this->assertEquals(true, $this->apiRequest->isLoggedIn());
    }

    public function testCookieJarInit()
    {
        vfsStream::setup();
        $cookiePath = vfsStream::url('root/path_to_cookies.txt');
        touch($cookiePath);

        $api = new ApiRequest('My UserAgent String', $cookiePath);
        $this->assertNotNull($cookiePath, $api->getCookieJar());
    }

    public function testGetReferer()
    {

    }
}

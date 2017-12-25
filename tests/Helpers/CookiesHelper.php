<?php

namespace seregazhuk\tests\Helpers;

use Mockery;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;

trait CookiesHelper
{
    /**
     * @var string
     */
    protected $cookieFilePath;

    /**
     * @param bool $withAuth
     * @param string $fileName
     */
    protected function createCookieFile($withAuth = true, $fileName = 'test')
    {
        $this->cookieFilePath = $this->getCookiePath($fileName);

        $content = "uk.pinterest.com        FALSE   /       TRUE    1506239919      csrftoken       123456";

        if($withAuth) {
            $content .= "\n#HttpOnly_.pinterest.com        TRUE    /       TRUE    1505894318      _auth   1";
        }

        if (file_exists($this->cookieFilePath)) unlink($this->cookieFilePath);

        file_put_contents($this->cookieFilePath, preg_replace('/ +/', "\t",$content));
    }

    /**
     * @param string $username
     * @return string
     */
    protected function getCookiePath($username = '')
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . HttpClient::COOKIE_PREFIX . "$username";
    }

    protected function tearDown()
    {
        if(file_exists($this->cookieFilePath)) {
            unlink($this->cookieFilePath);
        }

        Mockery::close();
    }
}

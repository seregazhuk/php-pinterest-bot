<?php

namespace seregazhuk\tests\Helpers;

trait CookiesHelper
{
    /**
     * @param string $fileName
     * @param bool $withAuth
     */
    protected function createCookieFile($fileName, $withAuth = true)
    {
        $content = "uk.pinterest.com        FALSE   /       TRUE    1506239919      csrftoken       123456";

        if($withAuth) {
            $content .= "\n#HttpOnly_.pinterest.com        TRUE    /       TRUE    1505894318      _auth   1";
        }

        unlink($fileName);
        file_put_contents($fileName, preg_replace('/ +/', "\t",$content));
    }
}
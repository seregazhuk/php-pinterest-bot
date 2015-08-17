<?php

namespace szhuk\PinterestAPI\helpers;

/**
 * Class UrlHelper
 */
class UrlHelper
{

    /**
     * @param $request
     * @return mixed
     */
    public static function buildRequestString($request)
    {
        return self::fixEncoding(http_build_query($request));
    }


    /**
     * Fix URL-encoding for some characters
     *
     * @param $str string
     * @return string
     */
    public static function fixEncoding($str)
    {
        return str_replace(
            ["%28", "%29", "%7E"],
            ["(", ")", "~"],
            $str
        );
    }
}
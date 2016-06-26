<?php

namespace seregazhuk\PinterestBot\Helpers;

class CsrfHelper
{
    const TOKEN_NAME = 'csrftoken';
    const DEFAULT_TOKEN = '1234';

    /**
     * Get a CSRF token from the given cookie file.
     *
     * @param string $file
     *
     * @return string|null
     */
    public static function getTokenFromFile($file)
    {
        if (!file_exists($file)) {
            return null;
        }

        foreach (file($file) as $line) {
            if ($token = self::parseLineForToken($line)) {
                return $token;
            }
        }

        return null;
    }

    /**
     * @param string $line
     *
     * @return false|string
     */
    protected static function parseLineForToken($line)
    {
        if (empty(strstr($line, self::TOKEN_NAME))) {
            return false;
        }

        preg_match('/' . self::TOKEN_NAME . '\s(\w*)/', $line, $matches);
        if (!empty($matches)) {
            return $matches[1];
        }

        return false;
    }

    /**
     * @return string
     */
    public static function getDefaultCookie()
    {
        return 'Cookie: csrftoken=' . self::DEFAULT_TOKEN . ';';
    }
}

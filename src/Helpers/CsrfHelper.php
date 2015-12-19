<?php

namespace seregazhuk\PinterestBot\Helpers;

class CsrfHelper
{
    const TOKEN_NAME = 'csrftoken';

    /**
     * Get a CSRF token from the given cookie file
     *
     * @param string $file
     * @return string
     */
    public static function getTokenFromFile($file)
    {
        if ( ! file_exists($file)) {
            return null;
        }

        foreach (file($file) as $line) {

            if ($token = self::_parseLineForToken($line)) {
                return $token;
            }
        }

        return null;
    }

    /**
     * @param string $line
     * @return bool
     */
    protected static function _parseLineForToken($line)
    {
        if (empty(strstr($line, self::TOKEN_NAME))) {
            return false;
        }

        preg_match('/' . self::TOKEN_NAME . '\s(\w*)/', $line, $matches);
        if ($matches) {
            return $matches[1];
        }

        return false;
    }
}

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
    public static function getCsrfToken($file)
    {

        if ( ! file_exists($file)) {
            return null;
        }

        foreach (file($file) as $line) {
            $line = trim($line);

            if ($line == "" || substr($line, 0, 2) == "# ") {
                continue;
            }

            $data = explode("\t", $line);

            if ($data[5] == self::TOKEN_NAME) {
                return $data[6];
            }

        }
        return null;
    }
}

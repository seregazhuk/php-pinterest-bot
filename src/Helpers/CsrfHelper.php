<?php

namespace seregazhuk\PinterestBot\Helpers;

class CsrfHelper
{
    /**
     * Get a CSRF token from the given cookie file
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

            if ($data[6] == "csrftoken") {
                return $data[7];
            }

        }

        return null;
    }
}

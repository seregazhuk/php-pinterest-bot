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

            if ($line == "" or substr($line, 0, 2) == "# ") {
                continue;
            }

            list($domain, $tailmatch, $path, $secure, $expires, $name, $value) = explode("\t", $line);

            if ($name == "csrftoken") {
                return $value;
            }

        }

        return null;

    }
}
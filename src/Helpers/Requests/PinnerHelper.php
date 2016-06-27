<?php

namespace seregazhuk\PinterestBot\Helpers\Requests;

use seregazhuk\PinterestBot\Api\Request;

class PinnerHelper
{
    /**
     * Creates Pinterest API request to login.
     *
     * @param string $username
     * @param string $password
     *
     * @return array
     */
    public static function createLoginQuery($username, $password)
    {
        $dataJson = [
            'username_or_email' => $username,
            'password'          => $password,
        ];

        return Request::createQuery($dataJson);
    }
}

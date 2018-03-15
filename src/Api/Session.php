<?php

namespace seregazhuk\PinterestBot\Api;

class Session
{
    /**
     * @var bool
     */
    private $isLoggedIn = false;

    /**
     * Get current auth status.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->isLoggedIn;
    }

    public function logout()
    {
        $this->isLoggedIn = false;
    }

    public function login()
    {
        $this->isLoggedIn = true;
    }
}

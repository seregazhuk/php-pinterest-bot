<?php

namespace seregazhuk\PinterestBot\Api;

class Session
{
    const DEFAULT_TOKEN = '1234';

    /**
     * @var bool
     */
    private $isLoggedIn = false;

    /**
     * @var string
     */
    private $csrfToken = '';

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
        $this->csrfToken = self::DEFAULT_TOKEN;
    }

    /**
     * @param string $token
     */
    public function login($token)
    {
        $this->isLoggedIn = true;
        $this->csrfToken = $token;
    }

    /**
     * @return bool
     */
    public function hasDefaultToken()
    {
        return $this->csrfToken === self::DEFAULT_TOKEN;
    }

    /**
     * @return bool
     */
    public function hasRealToken()
    {
        return !empty($this->csrfToken) && $this->csrfToken !== self::DEFAULT_TOKEN;
    }

    /**
     * @return string
     */
    public function token()
    {
        return $this->csrfToken;
    }
}

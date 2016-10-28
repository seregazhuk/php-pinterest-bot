<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class Auth extends Provider
{

    /**
     * @var array
     */
    protected $loginRequiredFor = [];

    const REGISTRATION_COMPLETE_EXPERIENCE_ID = '11:10105';
    const ACCOUNT_TYPE_OTHER = 'other';

    /**
     * Login as pinner.
     *
     * @param string $username
     * @param string $password
     *
     * @param bool $autoLogin
     * @return bool
     */
    public function login($username, $password, $autoLogin = true)
    {
        if ($this->request->isLoggedIn()) return true;

        $this->checkCredentials($username, $password);

        // Trying to load previously saved cookies from last login
        // session for this username.
        if($autoLogin && $this->request->autoLogin($username)) {
            return true;
        }

        return $this->processLogin($username, $password);
    }

    /**
     * If $removeCookies is set, cookie file will be removed
     * from the file system.
     *
     * @param bool $removeCookies
     */
    public function logout($removeCookies = false)
    {
        $this->request->logout($removeCookies);
    }

    /**
     * Register a new user.
     *
     * @param string $email
     * @param string $password
     * @param string $name
     * @param string $country
     * @param int $age
     *
     * @return bool
     */
    public function register($email, $password, $name, $country = 'GB', $age = 18)
    {
        $data = [
            "age"        => $age,
            "email"      => $email,
            "password"   => $password,
            "country"    => $country,
            "first_name" => $name,
            "container"  => 'home_page',
        ];

        return $this->makeRegisterCall($data);
    }

    /**
     * Register a new business account.
     *
     * @param string $email
     * @param string $password
     * @param string $businessName
     * @param string $website
     * @return bool|mixed
     */
    public function registerBusiness($email, $password, $businessName, $website = '')
    {
        $data = [
            "email"         => $email,
            "password"      => $password,
            "website_url"   => $website,
            "business_name" => $businessName,
            "account_type"  => self::ACCOUNT_TYPE_OTHER,
        ];

        return $this->makeRegisterCall($data);
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->request->isLoggedIn();
    }

    /**
     * Validates password and login.
     *
     * @param string $username
     * @param string $password
     */
    protected function checkCredentials($username, $password)
    {
        if (!$username || !$password) {
            throw new \LogicException('You must set username and password to login.');
        }
    }

    /**
     * @return bool
     */
    protected function completeRegistration()
    {
        $this->request->setTokenFromCookies();

        return $this->execPostRequest(
            ['placed_experience_id' => self::REGISTRATION_COMPLETE_EXPERIENCE_ID],
            UrlBuilder::RESOURCE_REGISTRATION_COMPLETE
        );
    }

    /**
     * @param array $data
     * @return bool|mixed
     */
    protected function makeRegisterCall($data)
    {
        $this->visitMainPage();
        $this->request->setTokenFromCookies();

        if (!$this->execPostRequest($data, UrlBuilder::RESOURCE_CREATE_REGISTER)) {
            return false;
        }

        return $this->completeRegistration();
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    protected function processLogin($username, $password)
    {
        $this->request->clearToken();

        $credentials = [
            'username_or_email' => $username,
            'password'          => $password,
        ];

        $response = $this->execPostRequest($credentials, UrlBuilder::RESOURCE_LOGIN, true);

        if (!$response->isOk()) return false;

        $this->request->login();

        return true;
    }

    public function visitMainPage()
    {
        $this->execGetRequest([], '');
    }
}
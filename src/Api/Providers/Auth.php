<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\SendsRegisterActions;

class Auth extends Provider
{
    use SendsRegisterActions;

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'logout'
    ];

    const REGISTRATION_COMPLETE_EXPERIENCE_ID = '11:10105';

    /**
     * Login as pinner.
     *
     * @param string $username
     * @param string $password
     * @param bool $autoLogin
     * @return bool
     */
    public function login($username, $password, $autoLogin = true)
    {
        if ($this->isLoggedIn()) return true;

        $this->checkCredentials($username, $password);

        // Trying to load previously saved cookies from last login session for this username.
        // Then grab user profile info to check, if cookies are ok. If an empty response
        // was returned, then send login request.
        if($autoLogin && $this->processAutoLogin($username)) {
            return true;
        }

        return $this->processLogin($username, $password);
    }

    public function logout()
    {
        $this->request->logout();
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
            "age"        => (string)$age,
            "email"      => $email,
            "password"   => $password,
            "country"    => $country,
            "first_name" => $name,
            "gender"     => "male",
            "container"  => 'home_page',
        ];

        return $this->makeRegisterCall($data);
    }

    /**
     * Register a new business account. At first we register a basic type account.
     * Then convert it to a business one. This is done to receive a confirmation
     * email after registration.
     *
     * @param string $email
     * @param string $password
     * @param string $businessName
     * @param string $website
     * @return bool|mixed
     */
    public function registerBusiness($email, $password, $businessName, $website = '')
    {
        $registration = $this->register($email, $password, $businessName);

        if(!$registration) return false;

        return $this->convertToBusiness($businessName, $website);
    }

    /**
     * Convert your account to a business one.
     *
     * @param string $businessName
     * @param string $websiteUrl
     * @return bool
     */
    public function convertToBusiness($businessName, $websiteUrl = '')
    {
        $data = [
            'business_name' => $businessName,
            'website_url'   => $websiteUrl,
            'account_type'  => 'other',
        ];

        return $this->execPostRequest($data, UrlBuilder::RESOURCE_CONVERT_TO_BUSINESS);
    }

    /**
     * @param string $link
     * @return array|bool
     */
    public function confirmEmail($link)
    {
        return $this->visitPage($link);
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
        $this->visitPage();

        if(!$this->sendEmailVerificationAction()) return false;

        if(!$this->execPostRequest($data, UrlBuilder::RESOURCE_CREATE_REGISTER)) return false;

        if(!$this->sendRegistrationActions()) return false;

        return $this->completeRegistration();
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    protected function processLogin($username, $password)
    {
        $this->request->dropCookies();

        $credentials = [
            'username_or_email' => $username,
            'password'          => $password,
        ];

        $response = $this->execPostRequest($credentials, UrlBuilder::RESOURCE_LOGIN, true);

        if ($response->hasErrors()) return false;

        $this->request->login();

        return true;
    }

    /**
     * @param string $username
     * @return bool
     */
    protected function processAutoLogin($username)
    {
        return $this->request->autoLogin($username) && $this->getProfile();
    }

    /**
     * @return array
     */
    protected function getProfile()
    {
        return $this->execGetRequest([], UrlBuilder::RESOURCE_GET_USER_SETTINGS);
    }
}
<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Response;
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
    const ACCOUNT_TYPE_OTHER = 'other';

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
        if ($this->request->isLoggedIn()) return true;

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
            "age"        => $age,
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
            "account_type"  => self::ACCOUNT_TYPE_OTHER,
            "business_name" => $businessName,
        ];

        return $this->makeBusinessRegisterCall($data);
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->request->isLoggedIn();
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
     * @return bool|Response
     */
    protected function sendEmailVerificationAction()
    {
        $actions = [
            ['name' => 'unauth.signup_step_1.completed']
        ];

        return $this->sendRegisterActionRequest($actions);
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

        if(!$this->sendPlainRegistrationActions()) return false;

        return $this->completeRegistration();
    }

    /**
     * @param array $data
     * @return bool|mixed
     */
    protected function makeBusinessRegisterCall($data)
    {
        $this->visitPage('business/create/');

        if(!$this->sendBusinessRegistrationInitActions()) return false;

        if(!$this->execPostRequest($data, UrlBuilder::RESOURCE_CREATE_REGISTER)) return false;

        if(!$this->sendBusinessRegistrationFinishActions()) return false;

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
        $this->request->getHttpClient()->removeCookies();

        $credentials = [
            'username_or_email' => $username,
            'password'          => $password,
        ];

        $response = $this->execPostRequest($credentials, UrlBuilder::RESOURCE_LOGIN, true);

        if (!$response->isOk()) return false;

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

    /**
     * @return bool
     */
    protected function sendRegisterActions()
    {
        $actions = [
            ["name" => "multi_step_step_2_complete"],
            ["name" => "signup_home_page"],
            ["name" => "signup_referrer.other"],
            ["name" => "signup_referrer_module.unauth_home_react_page"],
            ["name" => "unauth.signup_step_2.completed"],
            ["name" => "setting_new_window_location"],
        ];

        if(!$this->sendRegisterActionRequest($actions)) return false;

        if(!$this->sendRegisterActionRequest()) return false;

        return true;
    }
}
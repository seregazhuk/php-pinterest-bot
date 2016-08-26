<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use LogicException;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Exceptions\AuthException;
use seregazhuk\PinterestBot\Api\Traits\UploadsImages;

class User extends Provider
{
    use UploadsImages;

    /**
     * @var array
     */
    protected $loginRequiredFor = ['profile', 'convertToBusiness'];

    const REGISTRATION_COMPLETE_EXPERIENCE_ID = '11:10105';
    const ACCOUNT_TYPE_OTHER = 'other';

    /**
     * Update user profile info. Gets associative array as a param. Available keys of array are:
     * 'last_name', 'first_name', 'username', 'about', 'location' and 'website_url'.
     * You can also change user avatar by passing 'profile_image'.
     *
     * @param array $userInfo
     *
     * @return bool
     */
    public function profile($userInfo)
    {
        if (isset($userInfo['profile_image'])) {
            $userInfo['profile_image_url'] = $this->upload($userInfo['profile_image']);
        }

        return $this->execPostRequest($userInfo, UrlHelper::RESOURCE_UPDATE_USER_SETTINGS);
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
    public function register($email, $password, $name, $country = "UK", $age = 18)
    {
        $data = [
            "age"        => $age,
            "email"      => $email,
            "password"   => $password,
            "country"    => $country,
            "first_name" => $name,
            "container"  => "home_page"
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
     * Convert your account to a business one.
     *
     * @param string $businessName
     * @param string $websiteUrl
     *
     * @return bool
     */
    public function convertToBusiness($businessName, $websiteUrl = '')
    {
        $data = [
            'business_name' => $businessName,
            'website_url'   => $websiteUrl,
            'account_type'  => self::ACCOUNT_TYPE_OTHER,
        ];

        return $this->execPostRequest($data, UrlHelper::RESOURCE_CONVERT_TO_BUSINESS);
    }

    /**
     * Login as pinner.
     *
     * @param string $username
     * @param string $password
     *
     * @throws AuthException
     *
     * @return bool
     */
    public function login($username, $password)
    {
        if ($this->request->isLoggedIn()) return true;

        $this->checkCredentials($username, $password);
        $this->request->clearToken();

        $credentials = [
            'username_or_email' => $username,
            'password'          => $password,
        ];

        $response = $this->execPostRequest($credentials, UrlHelper::RESOURCE_LOGIN, true);
        if ($response->hasErrors()) {
            throw new AuthException($response->getLastError()['message']);
        }

        $this->request->login();

        return true;
    }

    public function logout()
    {
        $this->request->logout();
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
            throw new LogicException('You must set username and password to login.');
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
                UrlHelper::RESOURCE_REGISTRATION_COMPLETE
            );
    }

    /**
     * @param array $data
     * @return bool|mixed
     * @throws AuthException
     */
    protected function makeRegisterCall($data)
    {
        $this->execGetRequest([], '');
        $this->request->setTokenFromCookies();

        if (!$this->execPostRequest($data, UrlHelper::RESOURCE_CREATE_REGISTER)) {
            return false;
        }

        return $this->completeRegistration();
    }
}

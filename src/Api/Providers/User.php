<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Exceptions\AuthException;
use seregazhuk\PinterestBot\Helpers\Requests\PinnerHelper;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Api\Traits\UploadsImages;

class User extends Provider
{
    use UploadsImages;

    protected $loginRequiredFor = ['profile'];

    const REGISTRATION_COMPLETE_EXPERIENCE_ID = '11:10105';

    /**
     * Update user profile info. Gets associative array as a param. Available keys of array are:
     * 'last_name', 'first_name', 'username', 'about', 'location' and 'website_url'.
     * You can also change user avatar by passing 'profile_image'.
     *
     * @param array $userInfo
     *
     * @return mixed
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
     * @param string $county
     * @param int $age
     * @return bool
     */
    public function register($email, $password, $name, $county = "UK", $age = 18)
    {
        $this->execGetRequest([], '');
        $this->request->setTokenFromCookies();

        $data = [
            "age"        => $age,
            "email"      => $email,
            "password"   => $password,
            "country"    => $county,
            "first_name" => $name,
            "container"  => "home_page"
        ];

        if (!$this->execPostRequest($data, UrlHelper::RESOURCE_CREATE_REGISTER)) {
            return false;
        }

        return $this->completeRegistration();
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
        if ($this->request->isLoggedIn()) {
            return true;
        }

        $this->checkCredentials($username, $password);

        $postString = PinnerHelper::createLoginQuery($username, $password);
        $this->request->clearToken();

        $response = $this->request->exec(UrlHelper::RESOURCE_LOGIN, $postString);
        if ($this->response->hasErrors($response)) {
            throw new AuthException($this->response->getLastError()['message']);
        }
        $this->request->login();

        return true;
    }

    public function logout()
    {
        $this->request->logout();
    }

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

    protected function completeRegistration()
    {
        $this->request->setTokenFromCookies();

        return $this->execPostRequest(
            ['placed_experience_id' => self::REGISTRATION_COMPLETE_EXPERIENCE_ID], UrlHelper::RESOURCE_REGISTRATION_COMPLETE
        );
    }
}

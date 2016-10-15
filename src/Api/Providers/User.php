<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use LogicException;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\Traits\UploadsImages;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class User extends Provider
{
    use UploadsImages;

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'profile',
        'convertToBusiness',
        'changePassword',
        'isBanned',
        'deactivate',
        'getUserName',
        'invite'
    ];

    const REGISTRATION_COMPLETE_EXPERIENCE_ID = '11:10105';
    const ACCOUNT_TYPE_OTHER = 'other';

    /**
     * Updates or returns user profile info. Gets associative array as a param. Available keys of array are:
     * 'last_name', 'first_name', 'username', 'about', 'location' and 'website_url'.
     * You can also change user avatar by passing 'profile_image'.
     *
     * @param array $userInfo If empty returns current user profile.
     *
     * @return bool|array
     */
    public function profile($userInfo = [])
    {
        if(empty($userInfo)) {
            return $this->execGetRequest([], UrlBuilder::RESOURCE_GET_USER_SETTINGS);
        }

        if (isset($userInfo['profile_image'])) {
            $userInfo['profile_image_url'] = $this->upload($userInfo['profile_image']);
        }

        return $this->execPostRequest($userInfo, UrlBuilder::RESOURCE_UPDATE_USER_SETTINGS);
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

        return $this->execPostRequest($data, UrlBuilder::RESOURCE_CONVERT_TO_BUSINESS);
    }

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
     * Checks if current user is banned
     *
     * @return bool
     */
    public function isBanned()
    {
        $profile = $this->profile();

       return isset($profile['is_write_banned']) ?
           (bool)$profile['is_write_banned'] :
           false;
    }

    /**
     * Returns current user username
     *
     * @return string
     */
    public function getUserName()
    {
        $profile = $this->profile();

        return isset($profile['username']) ? $profile['username'] : '';
    }

    /**
     * @param string $oldPassword
     * @param string $newPassword
     * @return bool
     */
    public function changePassword($oldPassword, $newPassword)
    {
        $request = [
            'old_password'         => $oldPassword,
            'new_password'         => $newPassword,
            'new_password_confirm' => $newPassword,
        ];

        return $this->execPostRequest($request, UrlBuilder::RESOURCE_CHANGE_PASSWORD);
    }

    /**
     * Deactivates your account.
     *
     * @param string $reason
     * @param string $explanation
     * @return bool
     */
    public function deactivate($reason = 'other', $explanation = '')
    {
        $profile = $this->profile();

        if(!isset($profile['id'])) return false;

        $request = [
            'user_id'     => $profile['id'],
            'reason'      => $reason,
            'explanation' => $explanation,
        ];

        return $this->execPostRequest($request, UrlBuilder::RESOURCE_DEACTIVATE_ACCOUNT);
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

    /**
     * Send invite to email
     * @param string $email
     * @return bool|Response
     */
    public function invite($email)
    {
        $data = [
            'email' => $email,
            'type'  => 'email',
        ];

        return $this->execPostRequest($data, UrlBuilder::RESOURCE_INVITE);
    }
    
    /**
     * Ask for password reset link in email
     *
     * @param string $user Username or user mail
     * @return bool
     */
    public function sendPasswordResetLink($user)
    {
        $request = ['username_or_email' => $user];

        return $this->execPostRequest($request, UrlBuilder::RESOURCE_RESET_PASSWORD_SEND_LINK);
    }

    /**
     * Set a new password by link from reset password email
     *
     * @param $link
     * @param string $newPassword
     * @return bool|Response
     */
    public function resetPassword($link, $newPassword)
    {
        // Visit link to get current reset token, username and token expiration
        $this->execGetRequest([], $link);
        $this->request->clearToken();

        $passwordResetUrl = $this->request->getHttpClient()->getCurrentUrl();

        $urlData = parse_url($passwordResetUrl);
        $username = trim(str_replace('/pw/', '', $urlData['path']), '/');

        $query = [];
        parse_str($urlData['query'], $query);


        return $this->execPostRequest([
                'username'             => $username,
                'new_password'         => $newPassword,
                'new_password_confirm' => $newPassword,
                'token'                => $query['t'],
                'expiration'           => $query['e'],
            ], UrlBuilder::RESOURCE_RESET_PASSWORD_UPDATE, true);
    }


    public function visitMainPage()
    {
        $this->execGetRequest([], '');
    }
}

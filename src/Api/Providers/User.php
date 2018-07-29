<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\Forms\Profile;
use seregazhuk\PinterestBot\Api\Traits\HasProfileSettings;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\UploadsImages;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;

class User extends Provider
{
    use UploadsImages, HasProfileSettings;

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'id',
        'invite',
        'profile',
        'username',
        'isBanned',
        'deactivate',
        'sessionsHistory',
        'convertToBusiness',
    ];

    /**
     * Updates or returns user profile info. Gets associative array as a param. Available keys of array are:
     * 'last_name', 'first_name', 'username', 'about', 'location' and 'website_url'.
     * You can also change user avatar by passing 'profile_image'.
     *
     * @param array $userInfo If empty returns current user profile.
     *
     * @return bool|array|Profile
     */
    public function profile($userInfo = null)
    {
        // If we call method without params, return current user profile data.
        if (empty($userInfo)) {
            return $this->getProfile();
        }

        return $this->updateProfile($userInfo);
    }

    /**
     * Checks if current user is banned
     *
     * @return bool
     */
    public function isBanned()
    {
        return (bool)$this->getProfileData('is_write_banned');
    }

    /**
     * Returns current user username
     *
     * @return string
     */
    public function username()
    {
        return $this->getProfileData('username');
    }

    /**
     * Returns current user id
     *
     * @return string
     */
    public function id()
    {
        return $this->getProfileData('id');
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
        $userId = $this->id();

        if ($userId === null) {
            return false;
        }

        $request = [
            'user_id'     => $userId,
            'reason'      => $reason,
            'explanation' => $explanation,
        ];

        return $this->post(UrlBuilder::RESOURCE_DEACTIVATE_ACCOUNT, $request);
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

        return $this->post(UrlBuilder::RESOURCE_INVITE, $data);
    }

    /**
     * Remove things you’ve recently searched for from search suggestions.
     * @return bool|Response
     */
    public function clearSearchHistory()
    {
        return $this->post(UrlBuilder::RESOURCE_CLEAR_SEARCH_HISTORY);
    }

    /**
     * Simply makes GET request to some url.
     * @param string $url
     * @return array|bool
     */
    public function visitPage($url = '')
    {
        return $this->get($url);
    }

    /**
     * Get your account sessions history
     * @return array
     */
    public function sessionsHistory()
    {
        return $this->get(UrlBuilder::RESOURCE_SESSIONS_HISTORY);
    }

    /**
     * @return array
     */
    protected function getProfile()
    {
        return $this->get(UrlBuilder::RESOURCE_GET_USER_SETTINGS);
    }

    /**
     * @param array|Profile $userInfo
     * @return bool|Response
     */
    protected function updateProfile($userInfo)
    {
        // If we have a form object, convert it to array
        if ($userInfo instanceof Profile) {
            $userInfo = $userInfo->toArray();
        }

        if (isset($userInfo['profile_image'])) {
            $userInfo['profile_image_url'] = $this->upload($userInfo['profile_image']);
        }

        return $this->post(UrlBuilder::RESOURCE_UPDATE_USER_SETTINGS, $userInfo);
    }

    /**
     * @param string $key
     * @return mixed|string
     */
    protected function getProfileData($key)
    {
        $profile = $this->getProfile();

        return $profile[$key] ?? '';
    }
}

<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\Forms\Profile;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\UploadsImages;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;

class User extends Provider
{
    use UploadsImages;

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'invite',
        'profile',
        'username',
        'isBanned',
        'deactivate',
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
        if(empty($userInfo)) {
            return $this->get([], UrlBuilder::RESOURCE_GET_USER_SETTINGS);
        }

        // If we have a form object, convert it to array
        if($userInfo instanceof Profile) {
            $userInfo = $userInfo->toArray();
        }

        if (isset($userInfo['profile_image'])) {
            $userInfo['profile_image_url'] = $this->upload($userInfo['profile_image']);
        }

        return $this->post($userInfo, UrlBuilder::RESOURCE_UPDATE_USER_SETTINGS);
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
    public function username()
    {
        $profile = $this->profile();

        return isset($profile['username']) ? $profile['username'] : '';
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

        return $this->post($request, UrlBuilder::RESOURCE_DEACTIVATE_ACCOUNT);
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

        return $this->post($data, UrlBuilder::RESOURCE_INVITE);
    }

    /**
     * Remove things you’ve recently searched for from search suggestions.
     * @return bool|Response
     */
    public function clearSearchHistory()
    {
        return $this->post([], UrlBuilder::RESOURCE_CLEAR_SEARCH_HISTORY);
    }
}

<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;

class Password extends Provider
{
    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'change',
    ];

    /**
     * Ask for password reset link in email
     *
     * @param string $user Username or user mail
     * @return bool
     */
    public function sendResetLink($user)
    {
        $request = ['username_or_email' => $user];

        return $this->post($request, UrlBuilder::RESOURCE_RESET_PASSWORD_SEND_LINK);
    }

    /**
     * Set a new password by link from reset password email
     *
     * @param string $link
     * @param string $newPassword
     * @return bool|Response
     */
    public function reset($link, $newPassword)
    {
        // Visit link to get current reset token, username and token expiration
        $this->get([], $link);
        $this->request->dropCookies();

        $urlData = $this->parseCurrentUrl();
        $username = trim(str_replace('/pw/', '', $urlData['path']), '/');

        $query = [];
        parse_str($urlData['query'], $query);
        $request = [
            'username'             => $username,
            'new_password'         => $newPassword,
            'new_password_confirm' => $newPassword,
            'token'                => $query['t'],
            'expiration'           => $query['e'],
        ];

        return $this->post($request, UrlBuilder::RESOURCE_RESET_PASSWORD_UPDATE);
    }

    /**
     * @param string $oldPassword
     * @param string $newPassword
     * @return bool
     */
    public function change($oldPassword, $newPassword)
    {
        $request = [
            'old_password'         => $oldPassword,
            'new_password'         => $newPassword,
            'new_password_confirm' => $newPassword,
        ];

        return $this->post($request, UrlBuilder::RESOURCE_CHANGE_PASSWORD);
    }

    /**
     * @return mixed
     */
    protected function parseCurrentUrl()
    {
        $url = $this
            ->request
            ->getCurrentUrl();

        return parse_url($url);
    }
}
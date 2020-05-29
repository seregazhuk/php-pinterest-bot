<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use LogicException;
use seregazhuk\PinterestBot\Api\Forms\Registration;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;
use seregazhuk\PinterestBot\Api\Traits\ResolvesCurrentUser;
use seregazhuk\PinterestBot\Api\Traits\SendsRegisterActions;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class Auth extends Provider
{
    use SendsRegisterActions, ResolvesCurrentUser;

    const REGISTRATION_COMPLETE_EXPERIENCE_ID = '11:10105';
    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'logout',
        'convertToBusiness',
    ];

    public function logout()
    {
        $this->request->logout();
    }

    /**
     * Register a new business account. At first we register a basic type account.
     * Then convert it to a business one. This is done to receive a confirmation
     * email after registration.
     *
     * @param string|Registration $registrationForm
     * @param string $password
     * @param string $name
     * @param string $website
     * @return bool|mixed
     */
    public function registerBusiness($registrationForm, $password = null, $name = null, $website = '')
    {
        $registration = $this->register($registrationForm, $password, $name);

        if (!$registration) {
            return false;
        }

        // Re-Login
        $email = ($registrationForm instanceof Registration) ? $registrationForm->getEmail() : $registrationForm;
        $password = ($registrationForm instanceof Registration) ? $registrationForm->getPassword() : $password;
        if (!$this->login($email, $password)) {
            return false;
        }

        $website = ($registrationForm instanceof Registration) ?
            $registrationForm->getSite() : $website;

        $name = ($registrationForm instanceof Registration) ?
            $registrationForm->getName() : $name;

        return $this->convertToBusiness($name, $website);
    }

    /**
     * Register a new user.
     *
     * @param string|Registration $email
     * @param string|null $password
     * @param string|null $name
     * @param string $country @deprecated
     * @param int $age @deprecated
     *
     * @return bool
     */
    public function register($email, $password = null, $name = null, $country = 'GB', $age = 18)
    {
        $registrationForm = $this->getRegistrationForm($email, $password, $name, $country, $age);

        return $this->makeRegisterCall($registrationForm);
    }

    /**
     * @param string|Registration $email
     * @param string $password
     * @param string $name
     * @param string $country
     * @param string $age
     * @return Registration
     */
    protected function getRegistrationForm($email, $password, $name, $country, $age)
    {
        if ($email instanceof Registration) {
            return $email;
        }

        return $this->fillRegistrationForm(
            $email, $password, $name, $country, $age
        );
    }

    /**
     * @param string $registrationForm
     * @param string $password
     * @param string $name
     * @param string $country
     * @param string $age
     * @return Registration
     */
    protected function fillRegistrationForm($registrationForm, $password, $name, $country, $age)
    {
        return (new Registration($registrationForm, $password, $name))
            ->setCountry($country)
            ->setAge($age)
            ->setGender('male');
    }

    /**
     * @param Registration $registrationForm
     * @return bool|mixed
     */
    protected function makeRegisterCall(Registration $registrationForm)
    {
        if (!$this->sendEmailVerificationAction()) {
            return false;
        }

        if (!$this->post(UrlBuilder::RESOURCE_CREATE_REGISTER, $registrationForm->toArray())) {
            return false;
        }

        if (!$this->sendRegistrationActions()) {
            return false;
        }

        return $this->completeRegistration();
    }

    /**
     * @return bool
     */
    protected function completeRegistration()
    {
        return $this->post(
            UrlBuilder::RESOURCE_REGISTRATION_COMPLETE,
            ['placed_experience_id' => self::REGISTRATION_COMPLETE_EXPERIENCE_ID]
        );
    }

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
        if ($this->isLoggedIn()) {
            return true;
        }

        $this->checkCredentials($username, $password);

        // Trying to load previously saved cookies from last login session for this username.
        // Then grab user profile info to check, if cookies are ok. If an empty response
        // was returned, then send login request.
        if ($autoLogin && $this->processAutoLogin($username)) {
            return true;
        }

        return $this->processLogin($username, $password);
    }

    /**
     * Validates password and login.
     *
     * @param string $username
     * @param string $password
     * @throws LogicException
     */
    protected function checkCredentials($username, $password)
    {
        if (!$username || !$password) {
            throw new LogicException('You must set username and password to login.');
        }
    }

    /**
     * @param string $username
     * @return bool
     */
    protected function processAutoLogin($username)
    {
        return $this->request->autoLogin($username) && $this->resolveCurrentUserId();
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    protected function processLogin($username, $password)
    {
        $this->request->loadCookiesFor($username);

        $credentials = [
            'username_or_email' => $username,
            'password'          => $password,
        ];

        $this->post(UrlBuilder::RESOURCE_LOGIN, $credentials);

        if ($this->response->isEmpty()) {
            return false;
        }

        $this->request->login();

        return true;
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
            'business_name'   => $businessName,
            'website_url'     => $websiteUrl,
            'has_ads_credits' => '',
        ];

        return $this->post(UrlBuilder::RESOURCE_CONVERT_TO_BUSINESS, $data, false, '/business/convert/');
    }

    /**
     * @param string $link
     * @return array|bool
     */
    public function confirmEmail($link)
    {
        return $this->get($link);
    }
}

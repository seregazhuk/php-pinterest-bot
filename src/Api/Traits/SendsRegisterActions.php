<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

trait SendsRegisterActions
{
    use HandlesRequest;

    /**
     * @var array
     */
    protected $firstStepActions = [
        ['name' => 'unauth.signup_step_1.completed']
    ];

    /**
     * @var array
     */
    protected $secondStepActions = [
        ['name' => 'multi_step_step_2_complete'],
        ['name' => 'signup_home_page'],
        ['name' => 'signup_referrer.other'],
        ['name' => 'signup_referrer_module.unauth_home_react_page'],
        ['name' => 'unauth.signup_step_2.completed'],
        ['name' => 'setting_new_window_location'],
    ];

    /**
     * @return bool|Response
     */
    protected function sendEmailVerificationAction()
    {
        return $this->sendRegisterActionRequest($this->firstStepActions);
    }

    /**
     * @return bool
     */
    protected function sendRegistrationActions()
    {
        if (!$this->sendRegisterActionRequest($this->secondStepActions)) {
            return false;
        }

        return $this->sendRegisterActionRequest();
    }


    /**
     * @param array $actions
     * @return bool|Response
     */
    protected function sendRegisterActionRequest(array $actions = [])
    {
        return $this->post(
           UrlBuilder::RESOURCE_UPDATE_REGISTRATION_TRACK,
            ['secondStepActions' => $actions]
        );
    }
}

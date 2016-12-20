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
    protected $plainRegistrationActions = [
        ["name" => "multi_step_step_2_complete"],
        ["name" => "signup_home_page"],
        ["name" => "signup_referrer.other"],
        ["name" => "signup_referrer_module.unauth_home_react_page"],
        ["name" => "unauth.signup_step_2.completed"],
        ["name" => "setting_new_window_location"],
    ];

    /**
     * @var array
     */
    protected $businessRegistrationInitActions = [
        ["name" => "create_business_account_singlestep.loaded"],
        ["name" => "unauth_navigate.new_tab.BusinessAccountCreate"],

        ["name" => "create_business_account_singlestep.emailFieldFocused"],
        ["name" => "create_business_account_singlestep.passwordFieldFocused"],

        ["name" => "create_business_account_singlestep.passwordFieldFocused"],
        ["name" => "create_business_account_singlestep.businessNameFieldFocused"],
        ["name" => "create_business_account_singlestep.businessTypeFieldFocused"],
    ];

    protected $businessRegistrationFinishActions = [
        ["name" => "create_business_account_singlestep.submit_clicked"],
        ["name" => "signup_unknown_placement"],
        ["name" => "signup_referrer.direct"],
        ["name" => "signup_referrer_module.business_account_create"],
        ["name" => "create_business_account_singlestep.signup_success"],
        ["name" => "setting_new_window_location"],
    ];
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
    protected function sendPlainRegistrationActions()
    {
        if(!$this->sendRegisterActionRequest($this->plainRegistrationActions)) {
            return false;
        }

        return $this->sendRegisterActionRequest();
    }

    /**
     * @return bool
     */
    protected function sendBusinessRegistrationInitActions()
    {
        return $this->sendRegisterActionRequest([
            ["name" => "create_business_account_singlestep.loaded"],
            ["name" => "unauth_navigate.new_tab.BusinessAccountCreate"],
            ["name" => "create_business_account_singlestep.emailFieldFocused"],
            ["name" => "create_business_account_singlestep.passwordFieldFocused"],
            ["name" => "create_business_account_singlestep.businessNameFieldFocused"],
            ["name" => "create_business_account_singlestep.businessTypeFieldFocused"],
         ]);
    }

    /**
     * @return bool
     */
    protected function sendBusinessRegistrationFinishActions()
    {
        if(!$this->sendRegisterActionRequest($this->businessRegistrationFinishActions)) {
            return false;
        }

        return $this->sendRegisterActionRequest();
    }

    /**
     * @param array $actions
     * @return bool|Response
     */
    protected function sendRegisterActionRequest($actions = [])
    {
        return $this->execPostRequest(
            ['actions' => $actions], UrlBuilder::RESOURCE_UPDATE_REGISTRATION_TRACK
        );
    }
}
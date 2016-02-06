<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\FollowTrait;

class Interests extends Provider
{
    use FollowTrait;

    protected $loginRequired = [
        'follow',
        'unFollow'
    ];

    protected function getEntityIdName()
    {
        return Request::INTEREST_ENTITY_ID;
    }

    protected function getFollowUrl()
    {
        return UrlHelper::RESOURCE_FOLLOW_INTEREST;
    }

    protected function getUnfFollowUrl()
    {
        return UrlHelper::RESOURCE_UNFOLLOW_INTEREST;
    }
}

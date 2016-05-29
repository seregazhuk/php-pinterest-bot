<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\Followable;

class Interests extends Provider
{
    use Followable;

    protected $loginRequired = [
        'follow',
        'unFollow',
    ];

    protected function getEntityIdName()
    {
        return 'interest_id';
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

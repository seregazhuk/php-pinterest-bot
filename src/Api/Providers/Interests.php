<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Api\Traits\Followable;

class Interests extends Provider
{
    use Followable;

    protected $loginRequiredFor = ['follow', 'unFollow'];

    protected $followUrl   = UrlHelper::RESOURCE_FOLLOW_INTEREST;
    protected $unFollowUrl = UrlHelper::RESOURCE_UNFOLLOW_INTEREST;

    protected $entityIdName = 'interest_id';
}

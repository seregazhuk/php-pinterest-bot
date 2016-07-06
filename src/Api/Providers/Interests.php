<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Api\Traits\Followable;

class Interests extends Provider
{
    use Followable;

    /**
     * @var array
     */
    protected $loginRequiredFor = ['follow', 'unFollow'];

    protected $followUrl   = UrlHelper::RESOURCE_FOLLOW_INTEREST;
    protected $unFollowUrl = UrlHelper::RESOURCE_UNFOLLOW_INTEREST;

    protected $entityIdName = 'interest_id';

    /**
     * Get list of main categories
     * 
     * @return array|bool
     */
    public function getMain()
    {
        return $this->execGetRequest(["category_types" => "main"], UrlHelper::RESOURCE_GET_CATEGORIES);
    }

    /**
     * Get category info
     *
     * @param string $category
     * @return array|bool
     */
    public function getInfo($category)
    {
        return $this->execGetRequest(["category" => $category], UrlHelper::RESOURCE_GET_CATEGORY);
    }
}

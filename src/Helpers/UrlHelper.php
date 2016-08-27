<?php

namespace seregazhuk\PinterestBot\Helpers;

/**
 * Class UrlHelper.
 */
class UrlHelper
{
    /**
     * Login
     */
    const RESOURCE_LOGIN = 'resource/UserSessionResource/create/';

    /**
     * Boards
     */
    const RESOURCE_GET_BOARDS = 'resource/BoardsResource/get/';
    const RESOURCE_GET_BOARD_FEED = 'resource/BoardFeedResource/get/';
    const RESOURCE_PROFILE_BOARDS = 'resource/ProfileBoardsResource/get/';
    const RESOURCE_FOLLOW_BOARD = 'resource/BoardFollowResource/create/';
    const RESOURCE_UNFOLLOW_BOARD = 'resource/BoardFollowResource/delete/';
    const RESOURCE_DELETE_BOARD = 'resource/BoardResource/delete/';
    const RESOURCE_CREATE_BOARD = 'resource/BoardResource/create/';
    const RESOURCE_UPDATE_BOARD = 'resource/BoardResource/update/';
    const RESOURCE_BOARD_FOLLOWERS = 'resource/BoardFollowersResource/get/';

    /**
     * Pins
     */
    const RESOURCE_CREATE_PIN = 'resource/PinResource/create/';
    const RESOURCE_UPDATE_PIN = 'resource/PinResource/update/';
    const RESOURCE_REPIN = 'resource/RepinResource/create/';
    const RESOURCE_USER_FOLLOWERS = 'resource/UserFollowersResource/get/';
    const RESOURCE_DELETE_PIN = 'resource/PinResource/delete/';
    const RESOURCE_LIKE_PIN = 'resource/PinLikeResource/create/';
    const RESOURCE_UNLIKE_PIN = 'resource/PinLikeResource/delete/';
    const RESOURCE_COMMENT_PIN = 'resource/PinCommentResource/create/';
    const RESOURCE_COMMENT_DELETE_PIN = 'resource/PinCommentResource/delete/';
    const RESOURCE_PIN_INFO = 'resource/PinResource/get/';
    const RESOURCE_DOMAIN_FEED = 'resource/DomainFeedResource/get';
    const RESOURCE_ACTIVITY = 'resource/AggregatedActivityFeedResource/get';
    const RESOURCE_USER_FEED = 'resource/UserHomefeedResource/get/';

    /**
     * Pinners
     */
    const RESOURCE_FOLLOW_USER = 'resource/UserFollowResource/create/';
    const RESOURCE_UNFOLLOW_USER = 'resource/UserFollowResource/delete/';
    const RESOURCE_USER_INFO = 'resource/UserResource/get/';
    const RESOURCE_USER_FOLLOWING = 'resource/UserFollowingResource/get/';
    const RESOURCE_USER_PINS = 'resource/UserPinsResource/get/';

    /**
     * Search
     */
    const RESOURCE_SEARCH = 'resource/BaseSearchResource/get/';
    const RESOURCE_SEARCH_WITH_PAGINATION = 'resource/SearchResource/get/';

    /**
     * Interests.
     */
    const RESOURCE_FOLLOW_INTEREST = 'resource/InterestFollowResource/create/';
    const RESOURCE_UNFOLLOW_INTEREST = 'resource/InterestFollowResource/delete/';

    /**
     * Conversations.
     */
    const RESOURCE_SEND_MESSAGE = 'resource/ConversationsResource/create/';
    const RESOURCE_GET_LAST_CONVERSATIONS = 'resource/ConversationsResource/get/';

    /**
     * UserSettings
     */
    const RESOURCE_UPDATE_USER_SETTINGS = 'resource/UserSettingsResource/update/';

    /**
     * News
     */
    const RESOURCE_GET_LATEST_NEWS = 'resource/NetworkStoriesResource/get/';

    /**
     * Registration
     */
    const RESOURCE_CREATE_REGISTER = 'resource/UserRegisterResource/create/';
    const RESOURCE_CHECK_EMAIL = 'resource/EmailExistsResource/get';
    const RESOURCE_SET_ORIENTATION = 'resource/OrientationContextResource/create/';
    const RESOURCE_UPDATE_REGISTRATION_TRACK = 'resource/UserRegisterTrackActionResource/update/';
    const RESOURCE_REGISTRATION_COMPLETE = 'resource/UserExperienceCompletedResource/update/';
    const RESOURCE_CONVERT_TO_BUSINESS = 'resource/PartnerResource/update/';

    /**
     * Uploads
     */
    const IMAGE_UPLOAD = 'upload-image/';

    /**
     * Categories
     */
    const RESOURCE_GET_CATEGORIES = 'resource/CategoriesResource/get/';
    const RESOURCE_GET_CATEGORY = 'resource/CategoryResource/get/';

    const URL_BASE = 'https://uk.pinterest.com/';
    const HOST = 'uk.pinterest.com';

    /**
     * @param array $request
     *
     * @return mixed
     */
    public static function buildRequestString($request)
    {
        return self::fixEncoding(http_build_query($request));
    }

    /**
     * Appends resource url to base pinterest url.
     *
     * @param string $resourceUrl
     *
     * @return string
     */
    public static function buildApiUrl($resourceUrl)
    {
        return self::URL_BASE . $resourceUrl;
    }

    /**
     * Fix URL-encoding for some characters.
     *
     * @param string $str
     *
     * @return string
     */
    public static function fixEncoding($str)
    {
        return str_replace(
            ['%28', '%29', '%7E'], ['(', ')', '~'], $str
        );
    }

    /**
     * Return Pinterest API url for search requests.
     *
     * @param array $bookmarks
     * 
     * @return string
     */
    public static function getSearchUrl($bookmarks = [])
    {
        return empty($bookmarks) ?
            self::RESOURCE_SEARCH :
            self::RESOURCE_SEARCH_WITH_PAGINATION;
    }
}

<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;

trait HasProfileSettings
{
    use HandlesRequest;

    /**
     * Get list of available locales
     * @return array
     */
    public function getLocales()
    {
        return $this->get(UrlBuilder::RESOURCE_AVAILABLE_LOCALES);
    }

    /**
     * Get list of available countries
     * @return array
     */
    public function getCountries()
    {
        return $this->get(UrlBuilder::RESOURCE_AVAILABLE_COUNTRIES);
    }

    /**
     * Get list of available account types
     * @return array
     */
    public function getAccountTypes()
    {
        return $this->get(UrlBuilder::RESOURCE_AVAILABLE_ACCOUNT_TYPES);
    }
}

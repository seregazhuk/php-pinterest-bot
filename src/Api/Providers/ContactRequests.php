<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;

class ContactRequests extends Provider
{
    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'requests',
        'accept',
        'ignore',
    ];


    public function all()
    {
        $requests = $this->get([], UrlBuilder::RESOURCE_CONTACTS_REQUESTS);

        return !$requests ? [] : $requests;
    }

    /**
     * @param string $requestId
     * @return bool
     */
    public function accept($requestId)
    {
        $data = [
            'contact_request' => [
                "type" => "contactrequest",
                "id"   => $requestId,
            ],
        ];

        return $this->post($data, UrlBuilder::RESOURCE_CONTACT_REQUEST_ACCEPT);
    }

    /**
     * @param string $requestId
     * @return bool
     */
    public function ignore($requestId)
    {
        $data = [
            'contact_request' => [
                "type" => "contactrequest",
                "id"   => $requestId,
            ],
        ];

        return $this->post($data, UrlBuilder::RESOURCE_CONTACT_REQUEST_IGNORE);
    }
}

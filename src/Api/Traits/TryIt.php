<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

trait TryIt
{
    use HandlesRequest, UploadsImages;

    /**
     * @return array
     */
    protected function requiresLoginForTryIt()
    {
        return [
            'tryIt',
            'editTryIt',
            'deleteTryIt',
        ];
    }

    /**
     * @param string $pinId
     * @param array $additionalData
     * @param int $limit
     * @return Pagination
     */
    abstract protected function getAggregatedActivity($pinId, array $additionalData = [], $limit);

    /**
     * Makes a DidIt activity record.
     *
     * @param string $pinId
     * @param string $comment
     * @param null|string $pathToImage
     * @return array
     */
    public function tryIt($pinId, $comment, $pathToImage = null)
    {
        $data = $this->makeRequest($pinId, $comment, $pathToImage);

        $this->post(UrlBuilder::RESOURCE_TRY_PIN_CREATE, $data);

        return $this->getResponse()->getResponseData();
    }

    /**
     * @param string $pinId
     * @param string $tryItRecordId
     * @param string $comment
     * @param string|null $pathToImage
     * @return bool|Response
     */
    public function editTryIt($pinId, $tryItRecordId, $comment, $pathToImage = null)
    {
        $data = $this->makeRequest($pinId, $comment, $pathToImage);
        $data['user_did_it_data_id'] = $tryItRecordId;

        return $this->post(UrlBuilder::RESOURCE_TRY_PIN_EDIT, $data);
    }

    /**
     * Get the pinners who have tied this pin
     *
     * @param string $pinId
     * @param int $limit
     * @return Pagination
     */
    public function tried($pinId, $limit = Pagination::DEFAULT_LIMIT)
    {
        $data = [
            'field_set_key'    => 'did_it',
            'show_did_it_feed' => true,
        ];

        return $this->getAggregatedActivity($pinId, $data, $limit);
    }

    /**
     * @param string $tryItRecordId
     * @return bool|Response
     */
    public function deleteTryIt($tryItRecordId)
    {
        return $this->post(
            UrlBuilder::RESOURCE_TRY_PIN_DELETE, ['user_did_it_data_id' => $tryItRecordId]
        );
    }

    /**
     * @param string $pinId
     * @param string $comment
     * @param string|null $pathToImage
     * @return array
     */
    protected function makeRequest($pinId, $comment, $pathToImage = null)
    {
        $data = [
            'pin_id'  => $pinId,
            'details' => $comment,
        ];

        // If an image was specified try to upload it first to Pinterest simple upload to
        // receive and image url. Then we upload it to special DidIt resource to
        // get an image signature for the request.
        if ($pathToImage !== null) {
            $data['image_signature'] = $this->uploadImage($pathToImage);
        }

        return $data;
    }

    /**
     * @param string $pathToImage
     * @return string
     */
    protected function uploadImage($pathToImage)
    {
        $request = ['image_url' => $this->upload($pathToImage)];

        $this->post(UrlBuilder::RESOURCE_TRY_PIN_IMAGE_UPLOAD, $request);

        return $this->getResponse()->getResponseData('image_signature');
    }
}

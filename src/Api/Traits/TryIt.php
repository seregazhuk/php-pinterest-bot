<?php

namespace szhuk\src\Api\Traits;

use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\HandlesRequest;

trait TryIt
{
    use HandlesRequest;

    /**
     * Makes a DidIt activity record.
     *
     * @param string $pinId
     * @param string $comment
     * @param null|string $pathToImage
     * @return bool|Response
     */
    public function tryIt($pinId, $comment, $pathToImage = null)
    {
        $data = $this->makeRequest($pinId, $comment, $pathToImage);

        return $this->post($data, UrlBuilder::RESOURCE_TRY_PIN_CREATE);
    }

    /**
     * @param string $pinId
     * @param string $tryItRecordId
     * @param $comment
     * @param $pathToImage
     * @return bool|Response
     */
    public function editTryIt($pinId, $tryItRecordId, $comment, $pathToImage)
    {
        $data = $this->makeRequest($pinId, $comment, $pathToImage);
        $data['user_did_it_data_id'] = $tryItRecordId;

        return $this->post($data, UrlBuilder::RESOURCE_TRY_PIN_EDIT);
    }

    /**
     * @param string $tryItRecordId
     * @return bool|Response
     */
    public function deleteTryIt($tryItRecordId)
    {
        return $this->post(
            ['user_did_it_data_id' => $tryItRecordId],
            UrlBuilder::RESOURCE_TRY_PIN_DELETE
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
        if ($pathToImage) {
            $request = ['image_url' => $this->upload($pathToImage)];

            $this->post($request, UrlBuilder::RESOURCE_TRY_PIN_IMAGE_UPLOAD);

            $data['image_signatures'] = $this->getResponse()->getResponseData('image_signature');
        }

        return $data;
    }
}
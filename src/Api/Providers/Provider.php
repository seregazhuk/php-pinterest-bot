<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Contracts\RequestInterface;
use seregazhuk\PinterestBot\Contracts\ResponseInterface;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\ProviderTrait;

/**
 * Class Provider.
 */
abstract class Provider
{
    use ProviderTrait;

    /**
     * List of methods that require logged status.
     *
     * @var array
     */
    protected $loginRequired = [];

    /**
     * Instance of the API RequestInterface.
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * Instance of the API ResponseInterface.
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Executes a POST request to Pinterest API.
     *
     * @param array  $requestOptions
     * @param string $resourceUrl
     * @param bool   $returnData
     *
     * @return mixed
     */
    public function execPostRequest($requestOptions, $resourceUrl, $returnData = false)
    {
        $data = ['options' => $requestOptions];
        $postString = Request::createQuery($data);
        $response = $this->request->exec($resourceUrl, $postString);

        if ($returnData) {
            return $this->response->getData($response);
        }

        return $this->response->checkResponse($response);
    }

    public function execPaginatedRequest($data, $url, $sourceUrl, $bookmarks = [])
    {
        $data['options'] = $data;
        $response = $this->getRequest()->exec($url . '?' . Request::createQuery($data, $sourceUrl, $bookmarks));

        return $this->getResponse()->getPaginationData($response);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public function checkMethodRequiresLogin($method)
    {
        return in_array($method, $this->loginRequired);
    }
}

<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;

/**
 * Class Provider.
 * @property string entityIdName
 */
abstract class Provider
{
    /**
     * List of methods that require logged status.
     *
     * @var array
     */
    protected $loginRequiredFor = [];

    /**
     * Instance of the API Request.
     *
     * @var Request
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
    protected function execPostRequest($requestOptions, $resourceUrl, $returnData = false)
    {
        $postString = Request::createQuery($requestOptions);
        $response = $this->request->exec($resourceUrl, $postString);

        if ($returnData) {
            return $response->getData();
        }

        return !$response->hasErrors();
    }

    /**
     * Executes a GET request to Pinterest API.
     *
     * @param array $requestOptions
     * @param string $resourceUrl
     * @return array|bool
     */
    protected function execGetRequest(array $requestOptions = [], $resourceUrl = '')
    {
        $query = Request::createQuery($requestOptions);
        $response = $this->request->exec($resourceUrl . "?{$query}");
        
        return $this->response->getData($response);
    }

    /**
     * Executes a GET request to Pinterest API with pagination.
     *
     * @param array $requestOptions
     * @param string $resourceUrl
     * @param array $bookmarks
     * @return array|bool
     */
    protected function execGetRequestWithPagination(array $requestOptions, $resourceUrl, $bookmarks = [])
    {
        $query = Request::createQuery($requestOptions, $bookmarks);
        $response = $this->request->exec($resourceUrl . "?{$query}");

        return $this->response->getPaginationData($response);
    }

    public function getEntityIdName()
    {
        return property_exists($this, 'entityIdName') ? $this->entityIdName : '';
    }
    
    /**
     * Executes pagination GET request.
     *
     * @param array $data
     * @param string $url
     * @param array $bookmarks
     * @return array|bool
     */
    public function getPaginatedData(array $data, $url, $bookmarks = [])
    {
        return $this->execGetRequestWithPagination($data, $url, $bookmarks);
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public function checkMethodRequiresLogin($method)
    {
        return in_array($method, $this->loginRequiredFor);
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
}

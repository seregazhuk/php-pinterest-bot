<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Contracts\RequestInterface;
use seregazhuk\PinterestBot\Contracts\ResponseInterface;

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
    protected function execPostRequest($requestOptions, $resourceUrl, $returnData = false)
    {
        $postString = Request::createQuery(['options' => $requestOptions]);
        $response = $this->request->exec($resourceUrl, $postString);

        if ($returnData) {
            return $this->response->getData($response);
        }

        return $this->response->hasErrors($response);
    }

    /**
     * Executes a GET request to Pinterest API.
     *
     * @param array $requestOptions
     * @param string $resourceUrl
     * @return array|bool
     */
    protected function execGetRequest(array $requestOptions, $resourceUrl)
    {
        $query = Request::createQuery(['options' => $requestOptions]);
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
        $query = Request::createQuery(['options' => $requestOptions], $bookmarks);
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
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}

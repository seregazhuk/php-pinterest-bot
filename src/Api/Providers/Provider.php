<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\Pagination;

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
     * @param array $requestOptions
     * @param string $resourceUrl
     * @param bool $returnResponse
     *
     * @return Response|bool
     */
    protected function execPostRequest($requestOptions, $resourceUrl, $returnResponse = false)
    {
        $postString = Request::createQuery($requestOptions);
        $response = $this->request->exec($resourceUrl, $postString);

        return $returnResponse ? $response : $response->isOk();
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

        return $response->getResponseData();
    }

    /**
     * Executes a GET request to Pinterest API with pagination.
     *
     * @param array $requestOptions
     * @param string $resourceUrl
     * @param array $bookmarks
     * @return Response
     */
    protected function execGetRequestWithPagination(array $requestOptions, $resourceUrl, $bookmarks = [])
    {
        $query = Request::createQuery($requestOptions, $bookmarks);

        return $this->request->exec($resourceUrl . "?{$query}");
    }

    /**
     * @return string
     */
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
     * @return Response
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
     * @param array $params
     * @param int $limit
     * @param string $method
     * @return mixed
     */
    protected function getPaginatedResponse(array $params, $limit, $method = 'getPaginatedData')
    {
        return (new Pagination($this))->paginateOver($method, $params, $limit);
    }
}

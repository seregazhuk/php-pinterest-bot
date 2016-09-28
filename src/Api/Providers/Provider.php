<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use Generator;
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
     * @var bool
     */
    protected $returnData = true;


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
     * @var Response
     */
    protected $response;

    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
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

        $this->execute($resourceUrl, $postString);

        return $returnResponse ? $this->response : $this->response->isOk();

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

        $this->execute($resourceUrl . "?{$query}");

        return $this->response->getResponseData();
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

        $this->execute($resourceUrl . "?{$query}");

        return $this->response;
    }

    /**
     * @param $url
     * @param string $postString
     * @return $this
     */
    protected function execute($url, $postString = "")
    {
        $result = $this->request->exec($url, $postString);

        $this->processResult($result);

        return $this;
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
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->request->isLoggedIn();
    }

    /**
     * @param array $params
     * @param int $limit
     * @param string $method
     * @return Generator
     */
    protected function getPaginatedResponse(array $params, $limit, $method = 'getPaginatedData')
    {
        return (new Pagination($this))->paginateOver($method, $params, $limit);
    }

    /**
     * @param string $res
     * @return Response
     */
    protected function processResult($res)
    {
        return $this->response->fill(json_decode($res, true));
    }
}

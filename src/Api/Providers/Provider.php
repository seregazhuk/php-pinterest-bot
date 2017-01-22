<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\Pagination;

/**
 * Class Provider.
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
     * @param array $bookmarks
     * @return array|bool|Response
     */
    protected function execGetRequest(array $requestOptions = [], $resourceUrl = '', $bookmarks = null)
    {
        $query = Request::createQuery($requestOptions, $bookmarks);

        $this->execute($resourceUrl . "?{$query}");

        return is_null($bookmarks) ?
            $this->response->getResponseData() :
            $this->response;
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
     * @param mixed $data
     * @param string $resourceUrl
     * @param int $limit
     *
     * @return Pagination
     */
    protected function paginate($data, $resourceUrl, $limit = Pagination::DEFAULT_LIMIT)
    {
        return (new Pagination($limit))
            ->paginateOver(function($bookmarks = []) use ($data, $resourceUrl) {
                return $this->execGetRequest($data, $resourceUrl, $bookmarks);
            });
    }

    /**
     * Simply makes GET request to some url.
     * @param string $url
     * @return array|bool
     */
    public function visitPage($url = '')
    {
        return $this->execGetRequest([], $url);
    }

    /**
     * @param string $res
     */
    protected function processResult($res)
    {
        $this->response->fillFromJson($res);
    }
}

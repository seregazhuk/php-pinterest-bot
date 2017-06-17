<?php

namespace seregazhuk\PinterestBot\Api\Providers\Core;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Api\ProvidersContainer;

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
     * @var ProvidersContainer
     */
    protected $container;

    /**
     * @param ProvidersContainer $container
     * @internal param Request $request
     * @internal param Response $response
     */
    public function __construct(ProvidersContainer $container)
    {
        $this->container = $container;
        $this->request = $container->getRequest();
        $this->response = $container->getResponse();
    }

    /**
     * Executes a POST request to Pinterest API.
     *
     * @param array $requestOptions
     * @param string $resourceUrl
     *
     * @return Response|bool
     */
    protected function post($requestOptions, $resourceUrl)
    {
        $postString = Request::createQuery($requestOptions);

        $this->execute($resourceUrl, $postString);

        return $this->response->isOk();

    }

    /**
     * Executes a GET request to Pinterest API.
     *
     * @param array $requestOptions
     * @param string $resourceUrl
     * @return array|bool|Response
     */
    protected function get(array $requestOptions = [], $resourceUrl = '')
    {
        $query = Request::createQuery(
            $requestOptions,
            $this->response->getBookmarks()
        );

        $this->execute($resourceUrl . '?' . $query);

        return $this->response->getResponseData();

    }

    /**
     * @param $url
     * @param string $postString
     * @return $this
     */
    protected function execute($url, $postString = "")
    {
        $result = $this->request->exec($url, $postString);

        $this->response->fillFromJson($result);

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
        return $this
            ->paginateCustom(function () use ($data, $resourceUrl) {
                $this->get($data, $resourceUrl);
                return $this->response;
            })->take($limit);
    }

    /**
     * Accepts callback which should return PaginatedResponse object.
     *
     * @param callable $callback
     * @param int $limit
     * @return Pagination
     */
    protected function paginateCustom(callable $callback, $limit = Pagination::DEFAULT_LIMIT)
    {
        $this->response->clear();

        return (new Pagination)
            ->paginateOver($callback)
            ->take($limit);
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}

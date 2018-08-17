<?php

namespace seregazhuk\PinterestBot\Api\Providers\Core;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use function seregazhuk\PinterestBot\class_uses_recursive;
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
     * @return array
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
     * @param string $resourceUrl
     * @param array $requestOptions
     * @param bool $returnData
     * @return bool|array
     */
    public function post($resourceUrl, array $requestOptions = [], $returnData = false)
    {
        $postString = $this->request->createQuery($requestOptions);

        // When executing POST request we need a csrf-token.
        $this->initTokenIfRequired();

        $this->execute($resourceUrl, $postString);

        return $returnData ? $this->response->getResponseData() : $this->response->isOk();
    }

    /**
     * Executes a GET request to Pinterest API.
     *
     * @param string $resourceUrl
     * @param array $requestOptions
     * @return array|bool
     */
    protected function get($resourceUrl = '', array $requestOptions = [])
    {
        $this->execute($resourceUrl . $this->makeQueryString($requestOptions));

        return $this->response->getResponseData();
    }

    /**
     * @param string $url
     * @param string $postString
     * @return $this
     */
    protected function execute($url, $postString = '')
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
        $methodsThatRequireLogin = array_merge($this->loginRequiredFor, $this->requiresLoginFor());

        return in_array($method, $methodsThatRequireLogin);
    }

    /**
     * @return array
     */
    protected function requiresLoginFor()
    {
        $loginRequired = [];

        foreach (class_parents($this) + class_uses_recursive($this) as $traitOrParent) {
            $class = basename(str_replace('\\', '/', $traitOrParent));

            if (method_exists($traitOrParent, $method = 'requiresLoginFor' . $class)) {
                $loginRequired = array_merge($loginRequired, forward_static_call([$this, $method]));
            }
        }

        return $loginRequired;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->request->isLoggedIn();
    }

    /**
     * @param string $resourceUrl
     * @param mixed $data
     * @param int $limit
     * @return Pagination
     */
    protected function paginate($resourceUrl, $data, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginateCustom(
            function () use ($data, $resourceUrl) {
                $this->get($resourceUrl, $data);
                return $this->response;
            }
        )->take($limit);
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

        return (new Pagination)->paginateOver($callback)->take($limit);
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

    protected function initTokenIfRequired()
    {
        if ($this->request->hasToken()) {
            return;
        }

        // Simply visit main page to fill the cookies
        // and parse a token from them
        $this->get();
    }

    /**
     * @param array $requestOptions
     * @return string
     */
    protected function makeQueryString(array $requestOptions)
    {
        if (empty($requestOptions)) {
            return '';
        }

        return '?' . $this->request->createQuery(
            $requestOptions, $this->response->getBookmarks()
        );
    }
}

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
    }

    /**
     * Executes a POST request to Pinterest API.
     *
     * @param string $resourceUrl
     * @param array $requestOptions
     * @return Response
     */
    public function post($resourceUrl, array $requestOptions = [])
    {
        $postString = $this->request->createQuery($requestOptions);

        // When executing POST request we need a csrf-token.
        $this->initTokenIfRequired();

        return $this->execute($resourceUrl, $postString);
    }

    /**
     * Executes a GET request to Pinterest API.
     *
     * @param string $resourceUrl
     * @param array $requestOptions
     * @param array $bookmarks
     * @return Response
     */
    protected function get($resourceUrl = '', array $requestOptions = [], array $bookmarks = [])
    {
        $query = $this->request->createQuery(
            $requestOptions,
            $bookmarks
        );

        return $this->execute($resourceUrl . '?' . $query);
    }

    /**
     * @param string $url
     * @param string $postString
     * @return Response
     */
    protected function execute($url, $postString = '')
    {
        $result = $this->request->exec($url, $postString);

        return Response::fromJson($result);
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
        return $this
            ->paginateCustom(
                function () use ($data, $resourceUrl) {
                    return $this->get($resourceUrl, $data);
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
        return (new Pagination)
            ->paginateOver($callback)
            ->take($limit);
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
}

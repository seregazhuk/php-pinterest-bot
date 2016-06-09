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
    protected function execPostRequest($requestOptions, $resourceUrl, $returnData = false)
    {
        $postString = Request::createQuery(['options' => $requestOptions]);
        $response = $this->request->exec($resourceUrl, $postString);

        if ($returnData) {
            return $this->response->getData($response);
        }

        return $this->response->checkErrorInResponse($response);
    }

    /**
     * Executes a GET request to Pinterest API with pagination if required.
     *
     * @param array $requestOptions
     * @param string $resourceUrl
     * @param bool $needsPagination
     * @param array $bookmarks
     * @return array|bool
     */
    protected function execGetRequest(array $requestOptions, $resourceUrl, $needsPagination = false, $bookmarks = [])
    {
        $query = Request::createQuery(['options' => $requestOptions], $bookmarks);
        $response = $this->request->exec($resourceUrl . "?{$query}");
        
        if ($needsPagination) {
            return $this->response->getPaginationData($response);
        }

        return $this->response->getData($response);
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
        return $this->execGetRequest($data, $url, true, $bookmarks);
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

<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Contracts\RequestInterface;
use seregazhuk\PinterestBot\Contracts\ResponseInterface;
use seregazhuk\PinterestBot\Helpers\Providers\ProviderHelper;

/**
 * Class Provider
 *
 * @package seregazhuk\PinterestBot\Contracts
 */
class Provider
{
    use ProviderHelper;

    /**
     * Instance of the API RequestInterface
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * Instance of the API ResponseInterface
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Executes a POST request to Pinterest API
     *
     * @param array  $requestOptions
     * @param string $resourceUrl
     * @param bool   $checkLogin
     * @param bool   $returnData
     * @return mixed
     */
    public function callPostRequest($requestOptions, $resourceUrl, $checkLogin = false, $returnData = null)
    {
        if ($checkLogin) {
            $this->request->checkLoggedIn();
        }
        $data = array("options" => $requestOptions);
        $request = Request::createRequestData($data);

        $postString = UrlHelper::buildRequestString($request);
        $response = $this->request->exec($resourceUrl, $postString);

        if ($returnData) {
            return $this->response->getData($response);
        }

        return $this->response->checkResponse($response);
    }


    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    protected function getResponse()
    {
        return $this->response;
    }
}
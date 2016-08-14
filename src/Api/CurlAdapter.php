<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Contracts\Http;

/**
 * Class CurlAdapter.
 *
 * @property string $cookieJar
 * @property string $cookePath
 */
class CurlAdapter implements Http
{
    /**
     * Contains the curl instance.
     *
     * @var resource
     */
    private $curl;

    /**
     * Executes curl request.
     *
     * @param string $url
     * @param array $options
     *
     * @return string
     */
    public function execute($url, array $options = [])
    {
        $this->init($url)->setOptions($options);
        $res = curl_exec($this->curl);
        $this->close();

        return $res;
    }
    
    /**
     * Initializes curl resource.
     *
     * @param string $url
     *
     * @return $this
     */
    protected function init($url)
    {
        $this->curl = curl_init($url);

        return $this;
    }

    /**
     * Sets multiple options at the same time.
     *
     * @param array $options
     *
     * @return static
     */
    protected function setOptions(array $options = [])
    {
        curl_setopt_array($this->curl, $options);

        return $this;
    }

    /**
     * Get curl errors.
     *
     * @return string
     */
    public function getErrors()
    {
        return curl_error($this->curl);
    }

    /**
     * Close the curl resource.
     *
     * @return void
     */
    protected function close()
    {
        curl_close($this->curl);
    }
}

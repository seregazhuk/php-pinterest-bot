<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Contracts\HttpInterface;

/**
 * Class CurlAdapter
 *
 * @package seregazhuk\PinterestBot
 * @property string $cookieJar
 * @property string $cookePath
 */
class CurlAdapter implements HttpInterface
{
    /**
     * Contains the curl instance
     *
     * @var resource
     */
    private $curl;

    /**
     * Initializes curl resource
     *
     * @access public
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
     * Sets multiple options at the same time
     *
     * @access public
     * @param  array $options
     * @return static
     */
    protected function setOptions(array $options = [])
    {
        curl_setopt_array($this->curl, $options);

        return $this;
    }


    /**
     * Check if the curl request ended up with errors
     *
     * @access public
     * @return boolean
     */
    public function hasErrors()
    {
        return curl_errno($this->curl) ? true : false;
    }

    /**
     * Get curl errors
     *
     * @access public
     * @return string
     */
    public function getErrors()
    {
        return curl_error($this->curl);
    }

    /**
     * Close the curl resource
     *
     * @access public
     * @return void
     */
    protected function close()
    {
        curl_close($this->curl);
    }

    /**
     * Executes curl request
     *
     * @param string $url
     * @param array $options
     * @return array
     */
    public function execute($url, array $options = [])
    {
        $this->init($url)->setOptions($options);
        $res = curl_exec($this->curl);
        $this->close();

        return $res;
    }
}

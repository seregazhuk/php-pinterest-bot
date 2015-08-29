<?php

namespace seregazhuk\PinterestBot\Interfaces;

interface HttpInterface
{

    /**
     * Initializes curl resource
     *
     * @access public
     * @param string $url
     */
    public function init($url);

    /**
     * Sets an option in the curl instance
     *
     * @access public
     * @param  string $option
     * @param  string $value
     * @return $this
     */
    public function setOption($option, $value);

    /**
     * Sets multiple options at the same time
     *
     * @access public
     * @param  array $options
     * @return $this
     */
    public function setOptions(array $options = []);


    /**
     * Check if the curl request ended up with errors
     *
     * @access public
     * @return boolean
     */
    public function hasErrors();

    /**
     * Get curl errors
     *
     * @access public
     * @return string
     */
    public function getErrors();

    /**
     * Get curl info key
     *
     * @access public
     * @param  string $key
     * @return string
     */
    public function getInfo($key);

    /**
     * Close the curl resource
     *
     * @access public
     * @return void
     */
    public function close();


    /**
     * Executes curl request
     *
     * @return string
     */
    public function execute();
}

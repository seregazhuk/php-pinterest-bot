<?php

namespace seregazhuk\PinterestBot\Contracts;

interface HttpInterface
{
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
     * Executes curl request
     *
     * @param string $url
     * @param array $options
     * @return string
     */
    public function execute($url, array $options = []);
}

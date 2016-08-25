<?php

namespace seregazhuk\PinterestBot\Api\Contracts;

interface HttpClient
{
    /**
     * Get curl errors.
     *
     * @return string
     */
    public function getErrors();

    /**
     * Executes curl request.
     *
     * @param string $url
     * @param array  $options
     *
     * @return string
     */
    public function execute($url, array $options = []);
}

<?php

namespace seregazhuk\PinterestBot\Contracts;

interface HttpInterface
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

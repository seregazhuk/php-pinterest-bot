<?php

namespace seregazhuk\tests\Helpers;

trait SetsResponse
{
    /**
     * @param null|array $response
     * @param int $times
     * @param string $method
     * @return mixed
     */
    abstract protected function setResponseExpectation($response = null, $times = 1, $method = 'exec');
}
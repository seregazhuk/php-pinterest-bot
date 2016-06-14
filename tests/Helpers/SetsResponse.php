<?php

namespace seregazhuk\tests\Helpers;

trait SetsResponse
{
    abstract protected function setResponse($response, $times = 1, $method = 'exec');
}
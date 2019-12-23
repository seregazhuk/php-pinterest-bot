<?php

namespace seregazhuk\PinterestBot\Api;

class Error
{
    /**
     * @var mixed
     */
    private $errorData;

    /**
     * @param array|null $errorData
     */
    public function __construct($errorData = null)
    {
        $this->errorData = $errorData;
    }

    /**
     * @return string|null
     */
    public function getText()
    {
        if (isset($this->errorData['message_detail'])) {
            return $this->errorData['message_detail'];
        }

        if (isset($this->errorData['code'])) {
            return $this->errorData['code'];
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->errorData === null;
    }
}

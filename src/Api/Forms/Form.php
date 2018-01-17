<?php

namespace seregazhuk\PinterestBot\Api\Forms;

abstract class Form
{
    /**
     * @return array
     */
    abstract protected function getData();

    /**
     * @return array
     */
    public function toArray()
    {
        return array_filter($this->getData(), function ($item) {
            return !is_null($item);
        });
    }
}

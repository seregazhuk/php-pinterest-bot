<?php

namespace seregazhuk\PinterestBot\Api\Forms;

abstract class Form
{
    abstract protected function getData();

    public function toArray(){
        return array_filter($this->getData(), function($item){
            return !is_null($item);
        });
    }
}
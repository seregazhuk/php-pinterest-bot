<?php

namespace seregazhuk\PinterestBot\Helpers;

class ArrayHelper
{
    /**
     * @param string $key
     * @param array $data
     * @param mixed $default
     * @return array|bool|mixed
     */
    public static function getValueByKey($key = '', $data, $default = null)
    {
        if(empty($key)) return $data;

        $indexes = explode('.', $key);

        $value = $data;

        foreach ($indexes as $index) {
            if(!isset($value[$index])) return $default;

            $value = $value[$index];
        }

        return $value;
    }
}
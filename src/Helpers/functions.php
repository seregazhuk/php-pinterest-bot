<?php

namespace seregazhuk\PinterestBot;

/**
 * Returns all traits used by a class, its subclasses and trait of their traits.
 *
 * @param  object|string $class
 * @return array
 */
function class_uses_recursive($class)
{
    if (is_object($class)) {
        $class = get_class($class);
    }
    $results = [];
    foreach (array_merge([$class => $class], class_parents($class)) as $class) {
        $results += trait_uses_recursive($class);
    }
    return array_unique($results);
}

/**
 * Returns all traits used by a trait and its traits.
 *
 * @param  string $trait
 * @return array
 */
function trait_uses_recursive($trait)
{
    $traits = class_uses($trait);
    foreach ($traits as $trait) {
        $traits += trait_uses_recursive($trait);
    }
    return $traits;
}

/**
 * Returns array's value by key using dot notation.
 *
 * @param string $key
 * @param array $data
 * @param mixed $default
 * @return array|bool|mixed
 */
function get_array_data($key = '', $data, $default = null)
{
    if (empty($key)) {
        return $data;
    }

    $indexes = explode('.', $key);

    $value = $data;

    foreach ($indexes as $index) {
        if (!isset($value[$index])) {
            return $default;
        }

        $value = $value[$index];
    }

    return $value;
}

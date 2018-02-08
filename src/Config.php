<?php

namespace src;

/**
 * Class Config
 * @package src
 */
class Config
{
    /**
     * @param $key
     * @return mixed
     */
    public static function get($key){
        $data = require __DIR__ . '/../_config.php';
        return $data[$key];
    }
}
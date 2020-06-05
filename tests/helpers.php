<?php

if (!function_exists('config')) {
    /**
     * @param $key
     * @param null $default
     * @return array|mixed|null
     */
    function config($key, $default = null)
    {
        /**
         * @var \OneSite\Core\Builder\Config $config
         */
        $config = \OneSite\Core\Builder\Config::getInstance()->getConfigs();

        return $config->get($key, $default);
    }
}

if (!function_exists('env')) {
    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    function env($key, $default = null)
    {
        return !empty($_ENV[$key]) ? $_ENV[$key] : $default;
    }
}

if (!function_exists('storage_path')) {
    /**
     * @param string $path
     * @return string
     */
    function storage_path($path = "")
    {
        return realpath("storage/" . $path);
    }
}

if (!function_exists('base_path')) {
    /**
     * @param string $path
     * @return string
     */
    function base_path($path = "")
    {
        return realpath($path);
    }
}

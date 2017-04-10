<?php

namespace Shulha\Framework\DI;

/**
 * Class Service
 * @package Shulha\Framework\DI
 */
class Service
{
    /**
     * @var array   Service register
     */
    protected static $services = [];

    /**
     * Get service instance
     *
     * @param $service_name
     *
     * @return mixed
     */
    public static function get($service_name){
        if(!array_key_exists($service_name, self::$services)){
            self::set($service_name, Injector::make($service_name));
        }

        return self::$services[$service_name];
    }

    /**
     * Set service
     *
     * @param $service_name
     * @param $instance
     */
    public static function set($service_name, $instance){
        self::$services[$service_name] = $instance;
    }
}
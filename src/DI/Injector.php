<?php

namespace Shulha\Framework\DI;

/**
 * Class ServiceFactory
 * @package Shulha\Framework\DI
 */
class Injector
{
    /**
     * @var array
     */
    const SCALAR_TYPES = ['int', 'bool', 'string', 'float', 'array'];

    /**
     * @var array   Interface mapping
     */
    protected static $interface_mapping = [];

    /**
     * @var array   Config
     */
    public static $config = [];

    /**
     * @var array   Singleton instance storages
     */
    protected static $instances = [];

    /**
     * Set config
     *
     * @param $cfg
     */
    public static function setConfig($cfg){
        self::$config = $cfg;
        self::$interface_mapping = $cfg['services'] ?? [];
    }

    /**
     * Get path to contractor
     *
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public static function getInterface($name){
        if(!array_key_exists($name, self::$interface_mapping)){
            throw new \Exception("Services config have not contractor \"$name\"");
        }

        return self::$interface_mapping[$name];
    }

    /**
     * Resolve dependency by class/interface name
     *
     * @param   $class_name
     * @param array $params
     *
     * @return mixed
     * @throws \Exception
     */
    public static function make($class_name, $params = []){

        if(array_key_exists($class_name, self::$interface_mapping)){
            // Replace with actual class name:
            $class_name = self::$interface_mapping[$class_name];
        }

        // If service is already registered in singleton cache:
        if(isset(self::$instances[$class_name])){
            return self::$instances[$class_name];
        }

        if(class_exists($class_name)){
            try{
                $reflection_class = new \ReflectionClass($class_name);

                $reflection = $reflection_class;

                $constructor = self::getCreationMethod($reflection);

                while(empty($constructor) && !empty($reflection)){
                    // Fallback to parent constructor
                    $reflection = $reflection->getParentClass();
                    $constructor = $reflection ?  self::getCreationMethod($reflection) : null;
                }

                $constructor_params = empty($constructor) ? [] : $constructor->getParameters();
                // Mix provided params with config ones
                $params = array_merge(self::lookupConfigParams(self::getClassSlug($class_name)), $params);
                $paramset = self::resolveParams($constructor_params, $params);

                if(self::isSingletonPattern($reflection_class)){
                    $instance = call_user_func_array([$class_name, 'getInstance'], $paramset);
                    // Store instance in singleton cache:
                    self::$instances[$class_name] = $instance;
                } else {
                    $instance = $reflection_class->newInstanceArgs($paramset);
                }

                return $instance;
            } catch(\Exception $e) {
                throw new \Exception('Unable to resolve class '. $class_name . ': ' . $e->getMessage());
            }
        } else {
           return null;
        }
    }

    /**
     * Resolve params required by class constructor
     *
     * @param array $requested_params
     * @param array $actual_params
     *
     * @return mixed
     * @throws \Exception
     */
    public static function resolveParams($requested_params = [], $actual_params = []) {
        $params = [];

        if(!empty($requested_params)){
            foreach($requested_params as $param){
                $name = $param->getName();
                if($param->hasType()){
                    $type = (string)$param->getType();
                    if(!in_array($type, self::SCALAR_TYPES)){
                        // Non scalar type - try to create it with make method:
                        $params[$name] = self::make($type);
                    } else {
                        // Scalar type - lookup among provided and default values:
                        if(array_key_exists($name, $actual_params)) {
                            $params[$name] =  $actual_params[$name];
                        } else {
                            if($param->isDefaultValueAvailable()) {
                                $param->getDefaultValue();
                            } else {
                                throw new \Exception(sprintf('Unable to find value param [%s]', $name));
                            }
                        }
                    }
                } else {
                    // Scalar type - lookup among provided and default values:
                    if(array_key_exists($name, $actual_params)) {
                        $params[$name] =  $actual_params[$name];
                    } else {
                        if($param->isDefaultValueAvailable()) {
                            $param->getDefaultValue();
                        } else {
                            throw new \Exception(sprintf('Unable to find value param [%s]', $name));
                        }
                    }
                }
            }
        }

        return $params;
    }

    /**
     * Get class slug
     *
     * @param $class_name
     *
     * @return string
     */
    private static function getClassSlug($class_name): string {
        $buffer = explode('\\', $class_name);
        $slug = array_pop($buffer);

        return strtolower($slug);
    }

    /**
     * Return service params in config
     *
     * @param $node_name
     *
     * @return array
     */
    private static function lookupConfigParams($node_name): array {
        $cfg_params = [];
        if(array_key_exists($node_name, self::$config)){
            $cfg_params = (array)self::$config[$node_name];
        }

        return $cfg_params;
    }

    /**
     * Returns reflection for the nethod, responsible for creating the instance
     *
     * @param \ReflectionClass $class_ref
     * @return \ReflectionMethod
     */
    private static function getCreationMethod(\ReflectionClass $class_ref) {
        if(self::isSingletonPattern($class_ref)){
            $method = $class_ref->getMethod('getInstance');
        } else {
            $method = $class_ref->getConstructor();
        }

        return $method;
    }

    /**
     * Check if class seems to be a singleton pattern:
     *
     * @param \ReflectionClass $class_ref
     * @return bool
     */
    private static function isSingletonPattern(\ReflectionClass $class_ref): bool {
        return $class_ref->hasMethod('getInstance');
    }
}
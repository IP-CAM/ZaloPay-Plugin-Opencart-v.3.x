<?php

namespace Zalopay\Sdk;

class Api {
    
    public function __construct($appId, $key1, $key2, $env)
    {
        Config::setConfig("app_id", $appId);
        Config::setConfig("key1", $key1);
        Config::setConfig("key2", $key2);   
        Config::setConfig("env", $env);
    }
    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $className = __NAMESPACE__.'\\'.ucwords($name);

        $entity = new $className();

        return $entity;
    }
}
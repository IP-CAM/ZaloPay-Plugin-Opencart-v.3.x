<?php

namespace Zalopay\Sdk;

class Config {
  private static $config;

  public static function setConfig($key, $value){
    self::$config[$key] = $value;
  }

  static function init() {
    self::$config = Json::parseFile(__DIR__."/config.json");
  }

  static function get() {
    return self::$config;
  }
}

Config::init();
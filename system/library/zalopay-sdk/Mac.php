<?php
namespace Zalopay\Sdk;

class Mac
{
  static function compute(string $params, string $key = null)
  {
    if (is_null($key)) {
      $key = Config::get()['key1'];
    }
    return hash_hmac("sha256", $params, $key);
  }

  private static function createOrderMacData(Array $order)
  {
    return $order["app_id"]."|".$order["app_trans_id"]."|".$order["app_user"]."|".$order["amount"]
      ."|".$order["app_time"]."|".$order["embed_data"]."|".$order["item"];
  }

  static function createOrder(Array $order)
  {
    return self::compute(self::createOrderMacData($order));
  }

  static function getOrderStatus(Array $params)
  {
    return self::compute($params['app_id']."|".$params['app_trans_id']."|".Config::get()['key1']);
  }

  static function redirect(Array $params , string $key2)
  {
    return self::compute($params['appid']."|".$params['apptransid']."|".$params['pmcid']."|".$params['bankcode']
      ."|".$params['amount']."|".$params['discountamount']."|".$params["status"], $key2);
  }
}
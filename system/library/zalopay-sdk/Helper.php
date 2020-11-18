<?php

namespace Zalopay\Sdk;

class Helper
{
	private static $UID;

	private static function getTimestamp()
	{
		return round(microtime(true) * 1000);
	}

	static function init()
	{
		# Public key nhận được khi đăng ký ứng dụng với Zalopay
		self::$UID = self::getTimestamp();
	}

	/**
	 * Kiểm callback có hợp lệ hay không 
	 * 
	 * @param Array $params ["data" => string, "mac" => string]
	 * @return Array ["returncode" => int, "returnmessage" => string]
	 */
	static function verifyCallback(array $params)
	{
		$data = $params["data"];
		$requestMac = $params["mac"];

		$result = [];
		$mac = Mac::compute($data, Config::get()['key2']);

		if ($mac != $requestMac) {
			$result['return_code'] = 2;
			$result['return_message'] = 'mac not equal';
		} else {
			$result['return_code'] = 1;
			$result['return_message'] = 'success';
		}

		return $result;
	}

	/**
	 * Kiểm callback có hợp lệ hay không 
	 * 
	 * @param Array $data - là query string mà Zalopay truyền vào redirect link ($_GET)
	 * @return bool
	 *  - true: hợp lệ
	 *  - false: không hợp lệ
	 */
	static function verifyRedirect(array $data)
	{
		$reqChecksum = $data["checksum"];
		$checksum = Mac::redirect($data, Config::get()['key2']);

		return $reqChecksum === $checksum;
	}

	/**
	 * Generate app_trans_id hoặc mrefundid
	 * 
	 * @return string
	 *  - app_trans_id có dạng yyMMddxxxxx
	 *  - mrefundid có dạng yyMMdd_app_id_xxxxx
	 */
	static function genTransID()
	{
		return date("ymd") . "_" . (++self::$UID);
	}

	static function generateOrderData(array $params)
	{
		$embed_data = [];
		$appTransID = self::GenTransID();
		if (array_key_exists("embed_data", $params)) {
			$embed_data = $params["embed_data"];
		}

		$orderData = [
			"app_id" => Config::get()["app_id"],
			"app_time" => self::getTimeStamp(),
			"app_trans_id" => $appTransID,
			"app_user" => array_key_exists("app_user", $params) ? $params["app_user"] : "demo",
			"item" => JSON::encode(array_key_exists("item", $params) ? $params["item"] : []),
			"embed_data" => JSON::encode($embed_data),
			"bank_code" =>  array_key_exists("bank_code", $params) ? $params["bank_code"] : "zalopayapp",
			"description" => array_key_exists("description", $params) ? $params['description'] : "[OpenCart Demo Shop] - Thanh toán đơn hàng #" . $appTransID,
			"amount" => $params['amount'],
			"callback_url" => array_key_exists("callback_url", $params) ? $params["callback_url"] : "",
		];

		return $orderData;
	}

	/**
	 * Nhận vào thông tin đơn hàng và tạo đơn hàng thông qua API "tạo đơn hàng"
	 * 
	 * @param Array $order - Thông tin đơn hàng
	 * @return Array - Kết quả tạo đơn hàng
	 */
	static function createOrder(array $order)
	{
		$order['mac'] = Mac::createOrder($order);
		$result = Http::postForm(Config::get()['api'][Config::get()['env']] . "/create", $order);
		return $result;
	}

	/**
	 * Nhận vào app_trans_id của đơn hàng và tiến hành truy vấn thông tin đơn hàng thông qua API "Truy vấn đơn hàng"
	 * 
	 * @param String $app_trans_id - app_trans_id của đơn hàng
	 * @return Array - Trạng thái đơn hàng
	 */
	static function getOrderStatus(string $app_trans_id)
	{
		$params = [
			"app_id" => Config::get()['app_id'],
			"app_trans_id" => $app_trans_id
		];
		$params["mac"] = Mac::getOrderStatus($params);
		return Http::postForm(Config::get()['api'][Config::get()['env']] . "query", $params);
	}
}

Helper::init();

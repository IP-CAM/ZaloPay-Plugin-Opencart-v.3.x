<?php
class ModelExtensionPaymentZalopayAtm extends Model {

	public function install() {
		$this->log('Installing module');
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "zalopay_order` (
				`app_trans_id` varchar(255) NOT NULL,
				`order_id` int(11) NOT NULL,
				`environment` varchar(10) NOT NULL,
				PRIMARY KEY `app_trans_id` (`app_trans_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "zalopay_order`");
		$this->log('Module uninstalled');
	}


	public function getOrder($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zalopay_order` WHERE `order_id` = '" . $order_id . "' LIMIT 1");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return false;
		}
	}

	public function log($data) {
		// if ($this->config->has('payment_zalopay_logging') && $this->config->get('payment_zalopay_logging')) {
			$log = new Log('zalopay.log');

			$log->write($data);
		// }
	}
}

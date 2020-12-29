<?php

class ModelExtensionPaymentzalopayCc extends Model
{
    public function getMethod($address, $total)
    {
        $this->language->load('extension/payment/zalopay_cc');

        $method_data = array(
            'code' => 'zalopay_cc',
            'title' => $this->language->get('text_title'),
            'terms' => '',
            'sort_order' => "desc"
        );

        return $method_data;
    }
    
	public function addOrder($order_info, $app_trans_id, $environment = 'sandbox') {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "zalopay_order` SET `order_id` = '" . (int)$order_info['order_id'] . "', `app_trans_id` = '" . $app_trans_id . "', `environment` = '" . $environment . "'");
		return $this->db->getLastId();
    }

    public function getZaloPayOrderId($app_trans_id){
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX. "zalopay_order`" ." o WHERE o.app_trans_id ='" . $app_trans_id ."'");
        return $order_query->row['order_id'];
    }

    public function getAppTransIdByOrderId($orderId){
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX. "zalopay_order`" ." o WHERE o.order_id ='" . $orderId ."'");
        return $order_query->row['app_trans_id'];
    }
    
    public function getPendingOrderList(){
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX. "order`" ." o WHERE o.date_added >= NOW() - INTERVAL 15 MINUTE AND o.order_status_id = 1");
        return $order_query->rows;
    }
}

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

    public function updateOrderCustomField($order_id, $custom_field){
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET custom_field = '" . $custom_field . "' WHERE order_id = '" . (int)$order_id . "'");
    }

    public function getOrderByCustomField($customField){
        $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.custom_field = \"" . $customField . "\"");

        return $order_query->row;
    }

    public function getPendingOrderList(){
        $order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.date_added >= NOW() - INTERVAL 15 MINUTE AND o.order_status_id = 1");

        return $order_query->rows;
    }
}

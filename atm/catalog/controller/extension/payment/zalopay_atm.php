<?php
require_once __DIR__.'/../../../../system/library/zalopay-sdk/Zalopay.php';


class ControllerExtensionPaymentZalopayAtm extends Controller {
	
	public function index() {

        $data['button_confirm'] = $this->language->get('button_confirm');
        
        $this->load->model('checkout/order');
		$this->load->model('extension/payment/zalopay_atm');
        try
        {
            $orderId = $this->session->data['order_id'];
            $api = $this->getApiIntance();
            $order = $this->model_checkout_order->getOrder($orderId);
            $data = [
                'app_user' => $order['telephone'],
                'amount' => $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false),
                'embed_data' => array('order_id' => $orderId, "bankgroup" => "ATM", 'redirecturl' => $this->url->link('extension/payment/zalopay_atm/redirect')),
                'bank_code' => "",
                'description' => $this->config->get('payment_zalopay_atm_description'),
                'callback_url' => $this->url->link('extension/payment/zalopay_atm/callback')
            ];

            $order_data = $api->helper->generateOrderData($data);
            // Store app_trans_id to db
            $this->model_extension_payment_zalopay_atm->updateOrderCustomField($orderId, $order_data['app_trans_id']);

            $zalopay_atm_order = $api->helper->createOrder($order_data);
            
        }
        catch(\Zalopay\Sdk\Errors\Error $e)
        {
            $this->log->write($e->getMessage());
            $this->session->data['error'] = $e->getMessage();
            echo "<div class='alert alert-danger alert-dismissible'> Something went wrong. Unable to create zalopay_atm Order Id.</div>";
            exit;
        }
        if (file_exists(DIR_TEMPLATE.$this->config->get('config_template').'/template/extension/payment/zalopay_atm')) 
        {
            return $this->load->view($this->config->get('config_template').'/template/extension/payment/zalopay_atm', $zalopay_atm_order);
        } 
        else 
        {
            return $this->load->view('extension/payment/zalopay_atm', $zalopay_atm_order);
        }

    }
    
	public function confirm() {
        $response = array("return_code" => 1, "return_message" => "ok");
        try{
            if ($this->session->data['payment_method']['code'] == 'zalopay_atm') {
                $this->load->model('checkout/order');
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 1);
            }
            if (isset($this->session->data['order_id'])) {
                $this->cart->clear();
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);
                unset($this->session->data['guest']);
                unset($this->session->data['comment']);
                unset($this->session->data['order_id']);
                unset($this->session->data['coupon']);
                unset($this->session->data['reward']);
                unset($this->session->data['voucher']);
                unset($this->session->data['vouchers']);
                unset($this->session->data['totals']);
            }
            
        }
        catch (Exception $e) {
            $response["return_code"] = 2;
            $response["return_message"] = $e::getMessage();
        }
        // $response['redirect'] = $this->url->link('checkout/success');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response));		
	}

    public function callback() {
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/zalopay_atm');
        $api = $this->getApiIntance();
        try{
            $requestData = json_decode(file_get_contents('php://input'), true);
            $response = $api->helper->verifyCallback($requestData);
            if($response["return_code"]){
                $_data = json_decode($requestData["data"], true);
                $order = $this->model_extension_payment_zalopay->getOrderByCustomField(json_encode(array($_data['app_trans_id'])));
                $this->model_checkout_order->addOrderHistory($order['order_id'], 5);
            }
            
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($response));
        }
        catch (Exception $e) {
            $response = array("return_code" => 2, "return_message" => $e->getMessage());
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($response));
        }
        
    }

    public function redirect() {
        $this->load->model('checkout/order');
		$this->load->model('extension/payment/zalopay_atm');
        $api = $this->getApiIntance();
        try{
            $requestData = $this->request->request;
            $order = $this->model_extension_payment_zalopay->getOrderByCustomField(json_encode(array($requestData['apptransid'])));
            if(isset($requestData["status"]) && $requestData["status"] == 1){
                // Checksum
                $isValid = $api->helper->verifyRedirect($requestData);
                if ($isValid){
                    $queryRes = $api->helper->getOrderStatus($requestData['apptransid']);
                    if($queryRes['return_code'] == 1){
                        $this->model_checkout_order->addOrderHistory($order['order_id'], 5);
                        $this->response->redirect($this->url->link('checkout/success', '', true));
                    }
                }
            }
            $this->model_checkout_order->addOrderHistory($order['order_id'], 10);
            $this->response->redirect($this->url->link('checkout/failure', '', true));
        }
        catch(Exception $e) {
            $this->response->redirect($this->url->link('checkout/failure', '', true));
        }
    }

    public function cron(){
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/zalopay_atm');
        $api = $this->getApiIntance();
        $response = Array("return_code" => 1, "return_message" => "ok");
        
        $pendingOrderList = $this->model_extension_payment_zalopay_atm->getPendingOrderList();
        foreach ( $pendingOrderList as $pendingOrder ) {
            $queryRes = $api->helper->getOrderStatus($pendingOrder['custom_field']);
            if($queryRes['return_code'] == 1){
                $this->model_checkout_order->addOrderHistory($pendingOrder['order_id'], 5);
            }
            if($queryRes['return_code'] == 2){
                $this->model_checkout_order->addOrderHistory($pendingOrder['order_id'], 10);
            }
        }
        $response['total_order'] = count($pendingOrderList);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response));
    }

    protected function getApiIntance()
    {
        return new Zalopay\Sdk\Api($this->config->get("payment_zalopay_atm_app_id"), $this->config->get('payment_zalopay_atm_key1'), $this->config->get('payment_zalopay_atm_key2', $this->config->get('payment_zalopay_atm_environment')));
    }
}

<?php
require_once __DIR__.'/../../../../system/library/zalopay-sdk/Zalopay.php';


class ControllerExtensionPaymentZalopayCc extends Controller {
	
	public function index() {
        $data['button_confirm'] = $this->language->get('button_confirm');
        $this->load->model('checkout/order');
		$this->load->model('extension/payment/zalopay_cc');
        try
        {
            $orderId = $this->session->data['order_id'];
            $api = $this->getApiIntance();
            $order = $this->model_checkout_order->getOrder($orderId);
            $data = [
                'app_user' => $order['telephone'],
                'amount' => $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false),
                'embed_data' => array('order_id' => $orderId, 'redirecturl' => $this->url->link('extension/payment/zalopay_cc/redirect')),
                'description' => $this->config->get('payment_zalopay_description'),	
                'callback_url' => $this->url->link('extension/payment/zalopay_cc/callback'),
                'bank_code' => 'CC'
            ];
            $order_data = $api->helper->generateOrderData($data);

            // Store app_trans_id to db
            $this->model_extension_payment_zalopay_cc->addOrder($order, $order_data['app_trans_id']);
            $zalopay_order = $api->helper->createOrder($order_data);
            
        }
        catch(\Zalopay\Sdk\Errors\Error $e)
        {
            $this->log->write($e->getMessage());
            $this->session->data['error'] = $e->getMessage();
            echo "<div class='alert alert-danger alert-dismissible'> Something went wrong. Unable to create zalopay Order Id.</div>";
            exit;
        }
        if (file_exists(DIR_TEMPLATE.$this->config->get('config_template').'/template/extension/payment/zalopay_cc')) 
        {
            return $this->load->view($this->config->get('config_template').'/template/extension/payment/zalopay_cc', $zalopay_order);
        } 
        else 
        {
            return $this->load->view('extension/payment/zalopay_cc', $zalopay_order);
        }

    }
    
	public function confirm() {
        $response = array("return_code" => 1, "return_message" => "ok");
        try{
            if ($this->session->data['payment_method']['code'] == 'zalopay_cc') {
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
        $this->load->model('extension/payment/zalopay_cc');
        $api = $this->getApiIntance();
        try{
            $requestData = json_decode(file_get_contents('php://input'), true);
            $response = $api->helper->verifyCallback($requestData);
            if($response["return_code"]){
                $_data = json_decode($requestData["data"], true);
                $orderId = $this->model_extension_payment_zalopay_cc->getZaloPayOrderId($_data['app_trans_id']);
                if( $orderId > 0 ){
                    $this->model_checkout_order->addOrderHistory($orderId, 5);
                }
                else{
                    $error = 'Order not found!';
                    throw new Exception($error);
                }
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
		$this->load->model('extension/payment/zalopay_cc');
        $api = $this->getApiIntance();
        try{
            $requestData = $this->request->request;
            if(isset($requestData["status"]) && $requestData["status"] == 1){
                $orderId = $this->model_extension_payment_zalopay_cc->getZaloPayOrderId($requestData['apptransid']);
                // Checksum
                $isValid = $api->helper->verifyRedirect($requestData);
                if ($isValid){
                    $queryRes = $api->helper->getOrderStatus($requestData['apptransid']);
                    if($queryRes['return_code'] == 1){
                        if( $orderId > 0){
                            $this->model_checkout_order->addOrderHistory($orderId, 5);
                            $this->response->redirect($this->url->link('checkout/success', '', true));
                        }
                        else{
                            $error = 'Order not found!';
                            throw new Exception($error);
                        }
                    }
                }
            }
            $this->response->redirect($this->url->link('checkout/failure', '', true));
        }
        catch(Exception $e) {
            $this->response->redirect($this->url->link('checkout/failure', '', true));
        }
    }

    public function cron(){
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/zalopay_cc');
        $api = $this->getApiIntance();
        $response = Array("return_code" => 1, "return_message" => "ok");
        
        $pendingOrderList = $this->model_extension_payment_zalopay_cc->getPendingOrderList();
        try{
            foreach ( $pendingOrderList as $pendingOrder ) {
                $appTransId = $api->helper->getAppTransIdByOrderId($pendingOrder['order_id']);
                $queryRes = $api->helper->getOrderStatus($appTransId);
                if($queryRes['return_code'] == 1){
                    if( isset($order['order_id'])){
                        $this->model_checkout_order->addOrderHistory($pendingOrder['order_id'], 5);
                    }
                    else{
                        $error = 'Order not found!';
                        throw new Exception($error);
                    }
                }
                if($queryRes['return_code'] == 2){
                    if( isset($order['order_id'])){
                        $this->model_checkout_order->addOrderHistory($pendingOrder['order_id'], 10);
                    }
                    else{
                        $error = 'Order not found!';
                        throw new Exception($error);
                    }
                }
            }
        }
        catch (Exception $e){
            $response["return_code"] = 2;
            $response["return_message"] = $e->getMessage();
        }
        $response['total_order'] = count($pendingOrderList);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response));
    }

    protected function getApiIntance()
    {
        return new Zalopay\Sdk\Api(
            $this->config->get("payment_zalopay_cc_app_id"), 
            $this->config->get('payment_zalopay_cc_key1'), 
            $this->config->get('payment_zalopay_cc_key2'), 
            $this->config->get('payment_zalopay_cc_environment')
        );
    }
}

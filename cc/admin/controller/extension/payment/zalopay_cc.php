<?php

class ControllerExtensionPaymentzalopayCc extends Controller
{
    private $error = array();

    public function index()
    {
        $this->language->load('extension/payment/zalopay_cc');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_zalopay_cc', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['entry_key1'] = $this->language->get('entry_key1');
        $data['entry_key2'] = $this->language->get('entry_key2');
        $data['entry_app_id'] = $this->language->get('entry_app_id');
        $data['entry_callback_url'] = $this->language->get('entry_callback_url');
        $data['entry_redirect_url'] = $this->language->get('entry_redirect_url');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['help_key1'] = $this->language->get('help_key1');
        $data['help_key2'] = $this->language->get('help_key2');
        $data['help_redirect_url'] = $this->language->get('help_redirect_url');
        $data['help_callback_url'] = $this->language->get('help_callback_url');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['payment_zalopay_cc_app_id'])) {
            $data['error_app_id'] = $this->error['payment_zalopay_cc_app_id'];
        } else {
            $data['error_app_id'] = '';
        }

        if (isset($this->error['payment_zalopay_cc_key1'])) {
            $data['error_key1'] = $this->error['payment_zalopay_cc_key1'];
        } else {
            $data['error_key1'] = '';
        }

        if (isset($this->error['payment_zalopay_cc_key2'])) {
            $data['error_key2'] = $this->error['payment_zalopay_cc_key2'];
        } else {
            $data['error_key2'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token='.$this->session->data['user_token'], 'SSL'),
            'separator' => false,
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token='.$this->session->data['user_token'].'&type=payment', 'SSL'),
            'separator' => ' :: ',
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/zalopay_cc', 'user_token='.$this->session->data['user_token'], 'SSL'),
            'separator' => ' :: ',
        );

        $data['action'] = $this->url->link('extension/payment/zalopay_cc', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        if (isset($this->request->post['payment_zalopay_cc_status'])) {
            $data['zalopay_cc_status'] = $this->request->post['payment_zalopay_cc_status'];
        } else {
            $data['zalopay_cc_status'] = $this->config->get('payment_zalopay_cc_status');
        }

        if (isset($this->request->post['payment_zalopay_cc_app_id'])) {
            $data['zalopay_cc_app_id'] = $this->request->post['payment_zalopay_cc_app_id'];
        } else {
            $data['zalopay_cc_app_id'] = $this->config->get('payment_zalopay_cc_app_id');
        }

        if (isset($this->request->post['payment_zalopay_cc_key1'])) {
            $data['zalopay_cc_key1'] = $this->request->post['payment_zalopay_cc_key1'];
        } else {
            $data['zalopay_cc_key1'] = $this->config->get('payment_zalopay_cc_key1');
        }

        if (isset($this->request->post['payment_zalopay_cc_key2'])) {
            $data['zalopay_cc_key2'] = $this->request->post['payment_zalopay_cc_key2'];
        } else {
            $data['zalopay_cc_key2'] = $this->config->get('payment_zalopay_cc_key2');
		}

        if (isset($this->request->post['payment_zalopay_cc_environment'])) {
            $data['zalopay_cc_environment'] = $this->request->post['payment_zalopay_cc_environment'];
        } else {
            $data['zalopay_cc_environment'] = $this->config->get('payment_zalopay_cc_environment');
		}
		
        if (isset($this->request->post['payment_zalopay_cc_callback_url'])) {
            $data['zalopay_cc_callback_url'] = $this->request->post['payment_zalopay_cc_callback_url'];
        } else {
            $data['zalopay_cc_callback_url'] = $this->config->get('payment_zalopay_cc_callback_url');
		}
		
        if (isset($this->request->post['payment_zalopay_cc_redirect_url'])) {
            $data['zalopay_cc_redirect_url'] = $this->request->post['payment_zalopay_cc_redirect_url'];
        } else {
            $data['zalopay_cc_redirect_url'] = $this->config->get('payment_zalopay_cc_redirect_url');
        }
        
        // $data['callback_url'] = HTTPS_CATALOG . 'index.php?route=extension/payment/zalopay_cc/webhook';


        $this->template = 'extension/payment/zalopay_cc';
        $this->children = array(
            'common/header',
            'common/footer',
        );
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/zalopay_cc', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/zalopay_cc')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_zalopay_cc_app_id']) {
            $this->error['payment_zalopay_cc_app_id'] = $this->language->get('error_app_id');
        }

        if (!$this->request->post['payment_zalopay_cc_key1']) {
            $this->error['payment_zalopay_cc_key1'] = $this->language->get('error_key1');
		}
		

        if (!$this->request->post['payment_zalopay_cc_key2']) {
            $this->error['payment_zalopay_cc_key2'] = $this->language->get('error_key2');
		}
		
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}

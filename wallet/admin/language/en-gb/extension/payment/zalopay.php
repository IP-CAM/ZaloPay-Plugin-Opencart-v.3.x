<?php

// Heading
$_['heading_title'] = 'ZaloPay Wallet';

// Text 
$_['text_payment'] = 'Payment';
$_['text_environment_sandbox'] = 'Sandbox';
$_['text_environment_production'] = 'Production';
$_['text_extension'] = 'Extensions';
$_['text_edit'] = 'Edit Zalopay';
$_['text_success'] = 'Success: You have modified Zalopay account details!';
$_['text_zalopay'] = '<a href="https://zalopay.vn/" target="_blank"><img width="109" src="view/image/payment/zalopay.png" alt="Zalopay" title="ZaloPay" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_authorize'] = 'Authorize Only';
$_['text_capture'] = 'Authorize and Capture';

// Entry
$_['entry_status'] = 'Status';
$_['entry_key1'] = 'Zalopay Mac Key';
$_['entry_key2'] = 'Zalopay Callback Key';
$_['entry_app_id'] = 'App ID';
$_['entry_callback_url'] = 'Callback URL:';
$_['entry_redirect_url'] = 'Redirect URL:';
$_['entry_environment'] = 'Environment:';

//tooltips
$_['help_key1'] = 'The Mac Key you will recieve from the ZaloPay merchant tool at URL: https://sbmc.zalopay.vn/ ( sandbox ) and https://mc.zalopay.vn/ ( production ) . Use test Key for testing purposes.';
$_['help_key2'] = 'The Callback Key you will recieve from the ZaloPay merchant tool at URL: https://sbmc.zalopay.vn/ ( sandbox ) and https://mc.zalopay.vn/ ( production ) . Use test Key for testing purposes.';
$_['help_redirect_url'] = 'The UI redirect URL after user finished payment flow in ZaloPay Gateway';
$_['help_callback_url'] = 'Set Zalopay \'order.paid\' callbacks to call this URL with the below secret.';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify payment Zalopay!';
$_['error_key1'] = 'Mac Key Required!';
$_['error_key2'] = 'Callback Key Required!';
$_['error_app_id'] = 'App ID Required!';

# OpenCart ZaloPay Plugin

## Prequisties

- OpenCart 3.0 or above
- Web Server (Apache suggested)
- PHP 5.4+
- Database (MySQLi suggested)
- Please read this docs for imformation : https://docs.zalopay.vn/v2/docs/gateway/guide.html#1-chon-hinh-thuc-thanh-toan


## Setup
- Copy folder system to your OpenCart installation dir
- Every payment method is individual extension:
  - ZaloPay wallet --> copy all subfolder in `wallet` to your OpenCart installation dir
  - Visa/Master/JCB --> copy all subfolder in `cc` to your OpenCart installation dir
  - ATM --> copy all subfolder in `atm` to your OpenCart installation dir
- Go to Extensions -> Exntensions -> select ZaloPay Payment Method extension -> Install -> Config ZaloPay ( enable this extension and config app_id, key1, key2 )
- Your `redirect_url` `http://<your_domain>/index.php?route=extension/payment/zalopay/redirect`
- Your `callback_url` `http://<your_domain>/index.php?route=extension/payment/zalopay/callback`
- Create a schedule job using command "wget -t 1 - "http://<your_domain>/index.php?route=extension/payment/zalopay/cron" -O - | xargs echo >> /var/log/cron.log"
  - Implement schedule job must be at most 15 minute ( recommend schedule time is 1-2 minutes)
  - You can implement by using cron in Linux by using : `*/2 * * * * wget -t 1 - "http://<your_domain>/index.php?route=extension/payment/zalopay/cron" -O - | xargs echo >> /var/log/cron.log`
  - You also can implement by using Cpanel in this document : http://docs.opencart.com/en-gb/extension/cron/
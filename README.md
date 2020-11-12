# ZaloPay OpenCart plugin

## Prequisties

- OpenCart 3.0 or above
- Web Server (Apache suggested)
- PHP 5.4+
- Database (MySQLi suggested)

## Setup
- Copy all folder to your OpenCart installation
- Go to Extensions -> Exntensions -> select Payment -> Install ZaloPay -> Config ZaloPay ( config app_id key1 key2 )
- Your `redirect_url` `http://<your_domain>/index.php?route=extension/payment/zalopay/redirect`
- Your `callback_url` `http://<your_domain>/index.php?route=extension/payment/zalopay/callback`
- Create a schedule job using command "wget -t 1 - "http://<your_domain>/index.php?route=extension/payment/zalopay/cron" -O - | xargs echo >> /var/log/cron.log"
  - Implement schedule job must be at most 15 minute ( recommend schedule time is 1-2 minutes)
  - You can implement by using cron in Linux by using : `*/2 * * * * wget -t 1 - "http://<your_domain>/index.php?route=extension/payment/zalopay/cron" -O - | xargs echo >> /var/log/cron.log`
  - You also can implement by using Cpanel in this document : http://docs.opencart.com/en-gb/extension/cron/
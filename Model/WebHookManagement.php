<?php
namespace PayPalBR\PayPalPlus\Model;

use oauth;

class WebHookManagement
{


    /**
    * {@inheritdoc}
    */
    public function postWebHook($param)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/paypalplus.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Your text message');
        return 'Hello API! POST return the $param ' . $param;
    }


    /**
    * {@inheritdoc}
    */
    public function notificationUrl($param)
    {
        echo "<pre>";
        print_r($param);
        echo "</pre>";
        die;
    }

}
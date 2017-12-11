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

        return 'Hello API! POST return the $param ' . $param;
    }
}

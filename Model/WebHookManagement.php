<?php
namespace PayPalBR\PayPalPlus\Model;

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

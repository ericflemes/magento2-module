<?php
namespace PayPalBR\PayPalPlus\Api;

interface WebHookManagementInterface
{
    /**
     * POST for WebHook api
     * @param string $param
     * @return string
     */
    public function postWebHook($param);
}

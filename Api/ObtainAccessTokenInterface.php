<?php
namespace PayPalBR\PayPalPlus\Api;

interface ObtainAccessTokenInterface
{
    /**
     * POST for WebHook api
     * @return mixed
     */
    public function postAccessToken();
}

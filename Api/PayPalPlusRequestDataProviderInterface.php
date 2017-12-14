<?php

namespace PayPalBR\PayPalPlus\Api;


interface PayPalPlusRequestDataProviderInterface extends BaseRequestDataProviderInterface
{

    /**
     * @return int
     */
    public function getSaveCard();
}

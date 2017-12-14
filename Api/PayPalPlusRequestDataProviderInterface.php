<?php

namespace PayPalBR\PayPalPlus\Api;


interface PayPalPlusRequestDataProviderInterface extends BaseRequestDataProviderInterface
{

    /**
     * @return mixed
     */
    public function getPaypalPayerId();

    /**
     * @return mixed
     */
    public function getPayerIdCustomer();

    /**
     * @return mixed
     */
    public function getToken();

    /**
     * @return mixed
     */
    public function getTerm();
}

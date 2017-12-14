<?php

namespace PayPalBR\PayPalPlus\Gateway\Transaction\PayPalPlus\Config;

interface ConfigInterface
{
    const PATH_TEXT = 'payment/paypalbr_paypalplus/text';

    /**
     * @return string
     */
    public function getText();
}

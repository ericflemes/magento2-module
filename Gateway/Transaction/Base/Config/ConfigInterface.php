<?php

namespace PayPalBR\PayPalPlus\Gateway\Transaction\Base\Config;

interface ConfigInterface
{
    const PATH_PUBLIC_KEY_TEST    = 'payment/paypalbr_paypalplus/public_key_test';
    const PATH_SECRET_KEY_TEST    = 'payment/paypalbr_paypalplus/secret_key_test';
    const PATH_PUBLIC_KEY         = 'payment/paypalbr_paypalplus/public_key';
    const PATH_SECRET_KEY         = 'payment/paypalbr_paypalplus/secret_key';
    const PATH_TEST_MODE          = 'payment/paypalbr_paypalplus/test_mode';

    /**
     * @return string
     */
    public function getSecretKey();

    /**
     * @return string
     */
    public function getPublicKey();

    /**
     * @return string
     */
    public function getTestMode();

    /**
     * @return string
     */
    public function getBaseUrl();
}

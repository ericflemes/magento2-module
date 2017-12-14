<?php

namespace PayPalBR\PayPalPlus\Gateway\Transaction\Base\Config;

interface ConfigInterface
{
    const PATH_PUBLIC_KEY_TEST    = 'paypalbr_paypalplus/global/public_key_test';
    const PATH_SECRET_KEY_TEST    = 'paypalbr_paypalplus/global/secret_key_test';
    const PATH_PUBLIC_KEY         = 'paypalbr_paypalplus/global/public_key';
    const PATH_SECRET_KEY         = 'paypalbr_paypalplus/global/secret_key';
    const PATH_TEST_MODE          = 'paypalbr_paypalplus/global/test_mode';

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

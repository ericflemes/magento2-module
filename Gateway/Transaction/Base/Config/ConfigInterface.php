<?php
/**
 * Class ConfigInterface
 *
 * @author      Webjump Core Team <dev@webjump.com>
 * @copyright   2016 Webjump (http://www.webjump.com.br)
 * @license     http://www.webjump.com.br Copyright
 *
 * @link        http://www.webjump.com.br
 */

namespace PayPalBR\PayPalPlus\Gateway\Transaction\Base\Config;

interface ConfigInterface
{
    const PATH_PUBLIC_KEY_TEST    = 'paypal_plus/global/public_key_test';
    const PATH_SECRET_KEY_TEST    = 'paypal_plus/global/secret_key_test';
    const PATH_PUBLIC_KEY         = 'paypal_plus/global/public_key';
    const PATH_SECRET_KEY         = 'paypal_plus/global/secret_key';
    const PATH_TEST_MODE          = 'paypal_plus/global/test_mode';

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

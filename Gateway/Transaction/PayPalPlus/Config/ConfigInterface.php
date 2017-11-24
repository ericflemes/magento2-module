<?php
/**
 * Class ConfigInterface
 *
 * @author      Webjump Core Team <dev@webjump.com>
 * @copyright   2017 Webjump (http://www.webjump.com.br)
 * @license     http://www.webjump.com.br Copyright
 *
 * @link        http://www.webjump.com.br
 */

namespace PayPalBR\PayPalPlus\Gateway\Transaction\PayPalPlus\Config;


interface ConfigInterface
{
    const PATH_INSTRUCTIONS     = 'payment/paypal_plus/instructions';
    const PATH_TEXT             = 'payment/paypal_plus/text';
    const PATH_TYPE_BANK        = 'payment/paypal_plus/types';
    const PATH_EXPIRATION_DAYS  = 'payment/paypal_plus/expiration_days';

    /**
     * @return string
     */
    public function getInstructions();

    /**
     * @return string
     */
    public function getText();

    /**
     * @return string
     */
    public function getTypeBank();

    /**
     * @return string
     */
    public function getExpirationDays();
}

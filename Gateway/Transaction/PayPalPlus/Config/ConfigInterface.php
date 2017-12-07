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
    const PATH_TEXT = 'payment/paypalbr_paypalplus/text';

    /**
     * @return string
     */
    public function getText();
}

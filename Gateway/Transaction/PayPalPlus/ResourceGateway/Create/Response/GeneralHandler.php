<?php
/**
 * Class GeneralHandler
 *
 * @author      Webjump Core Team <dev@webjump.com>
 * @copyright   2016 Webjump (http://www.webjump.com.br)
 * @license     http://www.webjump.com.br Copyright
 *
 * @link        http://www.webjump.com.br
 */

namespace PayPalBR\PayPalPlus\Gateway\Transaction\PayPalPlus\ResourceGateway\Create\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use PayPalBR\PayPalPlus\Gateway\Transaction\Base\ResourceGateway\Response\AbstractHandler;

class GeneralHandler extends AbstractHandler implements HandlerInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _handle($payment, $response)
    {
        $payment->setTransactionId($response->id);
        $payment->setIsTransactionClosed(false);

        return $this;
    }
}

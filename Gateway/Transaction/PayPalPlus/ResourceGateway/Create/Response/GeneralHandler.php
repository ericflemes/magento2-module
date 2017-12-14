<?php

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

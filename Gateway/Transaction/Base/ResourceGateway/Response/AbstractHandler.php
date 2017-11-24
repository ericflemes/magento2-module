<?php
/**
 * Class AbstractHandler
 *
 * @author      Webjump Core Team <dev@webjump.com>
 * @copyright   2016 Webjump (http://www.webjump.com.br)
 * @license     http://www.webjump.com.br Copyright
 *
 * @link        http://www.webjump.com.br
 */

namespace PayPalBR\PayPalPlus\Gateway\Transaction\Base\ResourceGateway\Response;


use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;

abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @param $payment
     * @param $response
     * @return mixed
     */
    abstract protected function _handle($payment, $response);

    /**
     * {@inheritdoc}
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (
            ! isset($handlingSubject['payment']) ||
            ! $handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $response = $response['response'];
        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();
        /** @TODO CREATE A BUILD RESPONSE */
        $this->_handle($payment, $response);
    }
}

<?php

namespace PayPalBR\PayPal\Gateway\Transaction\PayPalPlus\ResourceGateway\Create\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use PayPalBR\PayPal\Gateway\Transaction\Base\ResourceGateway\Response\AbstractHandler;

class GeneralHandler extends AbstractHandler implements HandlerInterface
{
	const PENDING = "pending";
    /**
     * {@inheritdoc}
     */
    protected function _handle($payment, $response)
    {
    	// $response->getTransactions()[0]->getRelatedResources()[0]->getSale()->getState()
    	$transactions = $response->getTransactions();
    	foreach ($transactions as $id => $transaction) {
    		foreach ($transaction->getRelatedResources() as $id => $relatedResources) {
    			$sale = $relatedResources->getSale();
                $parentTransactionId = $payment->getAdditionalInformation('pay_id');
                $payment->setTransactionId($sale->getId());
                $payment->setParentTransactionId($parentTransactionId);
                $payment->setIsTransactionClosed(false);
                $payment->setAdditionalInformation('state_payPal', $sale->getState());
    		}
    	}

        return $this;
    }
}

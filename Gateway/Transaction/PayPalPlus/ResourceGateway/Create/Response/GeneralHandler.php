<?php

namespace PayPalBR\PayPalPlus\Gateway\Transaction\PayPalPlus\ResourceGateway\Create\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use PayPalBR\PayPalPlus\Gateway\Transaction\Base\ResourceGateway\Response\AbstractHandler;

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
                if ($sale->getState() == 'approved') {
                    $parentTransactionId = $payment->getAdditionalInformation('pay_id');
                    $payment
                        ->setTransactionId($sale->getId())
                        ->setParentTransactionId($parentTransactionId)
                        ->setIsTransactionClosed(true)
                        ->setAdditionalInformation('state_payPal', $sale->getState());
                }else{
                    $payment->setTransactionId($sale->getId());
                    $payment->setIsTransactionClosed(false);
                    $payment->setAdditionalInformation('state_payPal', $sale->getState());
                }
    		}
    	}

        return $this;
    }
}

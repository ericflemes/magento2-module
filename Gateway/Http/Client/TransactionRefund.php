<?php

namespace PayPalBr\PayPalPlus\Gateway\Http\Client;

use PayPalBr\PayPalPlus\Gateway\Request\PaymentDataBuilder;

class TransactionRefund extends AbstractTransaction
{
    /**
     * Process http request
     * @param array $data
     * @return Successful
     */
    protected function process(array $data)
    {
        $storeId = $data['store_id'] ?? null;

        unset($data['store_id']);

        return $this->adapterFactory->create($storeId)
            ->refund($data['transaction_id'], $data[PaymentDataBuilder::AMOUNT]);
    }
}

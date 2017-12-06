<?php

namespace PayPalBR\PayPalPlus\Model\Config\Source;

class Review extends \Magento\Sales\Model\Config\Source\Order\Status
{
	/**
     * @var string[]
     */
    protected $_stateStatuses = [
        \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW
    ];
}
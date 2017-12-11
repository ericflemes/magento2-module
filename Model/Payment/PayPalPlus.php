<?php

namespace PayPalBR\PayPalPlus\Model\Payment;
 
/**
 * Pay In Store payment method model
 */
class PayPalPlus extends \Magento\Payment\Model\Method\AbstractMethod
{
 
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'paypal_plus';
}
<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace PayPalBR\PayPalPlus\Model;

/**
 * Class Checkmo
 *
 * @method \Magento\Quote\Api\Data\PaymentMethodExtensionInterface getExtensionAttributes()
 *
 * @api
 * @since 100.0.2
 */
class PayPalPlus extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_PAYPALPLUS_CODE = 'paypal_plus';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_PAYPALPLUS_CODE;

    /**
     * @var string
     */
    protected $_formBlockType = \PayPalBR\PayPalPlus\Block\Form\PayPalPlus::class;

    /**
     * @var string
     */
    protected $_infoBlockType = \PayPalBR\PayPalPlus\Block\Info\PayPalPlus::class;
}

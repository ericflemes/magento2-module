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
    const PAYMENT_METHOD_PAYPALPLUS_CODE = 'paypalbr_paypalplus';

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

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \PayPalBR\PayPalPlus\Model\ConfigProvider $configProvider
    )
    {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
        $this->configProvider = $configProvider;
    }

    /**
     * Check if it is available
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if ( ! $this->configProvider->isActive() ) {
            return false;
        }
        return parent::isAvailable($quote);
    }
}

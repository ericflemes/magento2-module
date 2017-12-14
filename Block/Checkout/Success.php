<?php

namespace PayPalBR\PayPalPlus\Block\Checkout;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    const SCOPE_STORE = 'store';
    const XML_PATH_PENDING_PAYMENT_MESSAGE = 'payment/paypalbr_paypalplus/pending_payment_message';
    const XML_PATH_IS_METHOD_ACTIVE        = 'payment/paypalbr_paypalplus/active';
    const PAYPAL_LOGO                      = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logotipo_paypal_pagos_seguros.png';
    const PENDING_PAYMENT_STATUS_CODE      = 'payment_review';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $_scopeconfig;

    /**
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $_orderFactory;

    /**
     *
     * @var \Magento\Sales\Model\Order
     */
    private $_order = false;

    /**
     * Constructor method
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    )
    {
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
        $this->_scopeconfig = $context->getScopeConfig();
        $this->_orderFactory = $orderFactory;
    }

    /**
     * Returns if the payment is made by PayPal Plus
     * @return bool
     */
    protected function isPaymentPaypal()
    {
        $result = false;
        $payment = $this->_order->getPayment();
        if ($payment) {
            $code = $payment->getMethod();
            $result = ($code == \PayPalBR\PayPalPlus\Model\Payment\PayPalPlus::METHOD_NAME);
        }
        return $result;
    }

    /**
     * Get if method is active
     *
     * @return bool
     */
    public function getIsMethodActive()
    {
        if($this->isPaymentPaypal()) {
            return $this->getConfigValue(self::XML_PATH_IS_METHOD_ACTIVE);
        }
        return false;
    }

    /**
     * Load current Order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function  _initOrder()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $this->_order = $this->_orderFactory->create()->loadByIncrementId($this->getOrderId());
    }

    /**
     * Check if order has pending payment status
     *
     * @return boolean
     */
    public function isPaymentPending()
    {
        if($this->_order->getStatus() == self::PENDING_PAYMENT_STATUS_CODE){
            return true;
        }
        return false;
    }

    /**
     * Get if payment has pending status
     *
     * @return string
     */
    public function getPendingMessage()
    {
        if($this->isPaymentPending() && $this->_order->getPayment()->getMethod() == \PayPalBR\PayPalPlus\Model\Payment::CODE) {
            return $this->getConfigValue(self::XML_PATH_PENDING_PAYMENT_MESSAGE);
        }
        return '';
    }

    /**
     * Get Paypal logo for success page
     *
     * @return srtring
     */
    public function getPayPalLogo()
    {
        if($this->isPaymentPaypal()) {
           return self::PAYPAL_LOGO;
        }
    }

   /**
     * Get payment store config
     *
     * @return string
     */
    public function getConfigValue($configPath)
    {
        $value =  $this->_scopeConfig->getValue(
            $configPath,
            self::SCOPE_STORE
        );
        return $value;
    }
}

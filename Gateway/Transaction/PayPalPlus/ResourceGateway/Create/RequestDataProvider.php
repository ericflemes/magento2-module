<?php

namespace PayPalBR\PayPalPlus\Gateway\Transaction\PayPalPlus\ResourceGateway\Create;


use Magento\Checkout\Model\Session;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Model\InfoInterface;
use PayPalBR\PayPalPlus\Api\PayPalPlusRequestDataProviderInterface;
use PayPalBR\PayPalPlus\Gateway\Transaction\Base\ResourceGateway\Request\AbstractRequestDataProvider;
use PayPalBR\PayPalPlus\Gateway\Transaction\Base\Config\ConfigInterface;

class RequestDataProvider
    extends AbstractRequestDataProvider
    implements PayPalPlusRequestDataProviderInterface
{
    protected $config;

    public function __construct (
        OrderAdapterInterface $orderAdapter,
        InfoInterface $payment,
        Session $session,
        ConfigInterface $config
    )
    {
        parent::__construct($orderAdapter, $payment, $session);
        $this->setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaypalPayerId()
    {
        return $this->getPaymentData()->getAdditionalInformation('paypalPayerId');
    }

    /**
     * {@inheritdoc}
     */
    public function getPayerIdCustomer()
    {
        return $this->getPaymentData()->getAdditionalInformation('payerIdCustomer');
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->getPaymentData()->getAdditionalInformation('token');
    }

    /**
     * {@inheritdoc}
     */
    public function getTerm()
    {
        return $this->getPaymentData()->getAdditionalInformation('term');
    }

    /**
     * @return ConfigInterface
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @param ConfigInterface $config
     * @return $this
     */
    protected function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
        return $this;
    }
}

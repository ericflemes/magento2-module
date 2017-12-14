<?php

namespace PayPalBR\PayPalPlus\Gateway\Transaction\CreditCard\ResourceGateway\Create;


use Magento\Checkout\Model\Session;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Model\InfoInterface;
use PayPalBR\PayPalPlus\Api\PayPalPlusRequestDataProviderInterface;
use PayPalBR\PayPalPlus\Gateway\Transaction\Base\ResourceGateway\AbstractRequestDataProvider;
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
    public function getSaveCard()
    {
        return $this->getPaymentData()->getAdditionalInformation('save_card');
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

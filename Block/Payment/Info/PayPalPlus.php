<?php

namespace PayPalBR\PayPalPlus\Block\Payment\Info;


use Magento\Payment\Block\Info;
use Magento\Framework\DataObject;

class PayPalPlus extends Info
{
    const TEMPLATE = 'PayPalBR_PayPalPlus::info/paypalplus.phtml';

    public function _construct()
    {
        $this->setTemplate(self::TEMPLATE);
    }

    public function getTitle()
    {
        return $this->getInfo()->getAdditionalInformation('method_title');
    }

    public function getPayId()
    {
        return $this->getInfo()->getAdditionalInformation('pay_id');
    }

    public function getPayerId()
    {
        return $this->getInfo()->getAdditionalInformation('payer_id');
    }

    public function getToken()
    {
        return $this->getInfo()->getAdditionalInformation('token');
    }

    public function getTerm()
    {
        return $this->getInfo()->getAdditionalInformation('term');
    }

    public function getStatePayPal()
    {
        return $this->getInfo()->getAdditionalInformation('state_payPal');
    }
}
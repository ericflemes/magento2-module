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
}
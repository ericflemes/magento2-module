<?php
/**
 * Class Billet
 *
 * @author      Webjump Core Team <dev@webjump.com>
 * @copyright   2017 Webjump (http://www.webjump.com.br)
 * @license     http://www.webjump.com.br Copyright
 *
 * @link        http://www.webjump.com.br
 */

namespace PayPalBR\PayPalPlus\Block\Info;

use Magento\Payment\Block\Info;
use Magento\Framework\DataObject;

class PayPalPlus extends Info
{
    const TEMPLATE = 'PayPalBR_PayPalPlus::info/paypal-plus.html';

    public function _construct()
    {
        $this->setTemplate(self::TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = new DataObject([
            (string)__('Print Billet') => $this->getInfo()->getAdditionalInformation('billet_url')
        ]);

        $transport = parent::_prepareSpecificInformation($transport);
        return $transport;
    }

    public function getBilletUrl()
    {
        return $this->getInfo()->getAdditionalInformation('billet_url');
    }

    public function getTitle()
    {
        return $this->getInfo()->getAdditionalInformation('method_title');
    }
}
<?php
/**
 * Class ConfigProvider
 *
 * @author      Webjump Core Team <dev@webjump.com>
 * @copyright   2017 Webjump (http://www.webjump.com.br)
 * @license     http://www.webjump.com.br Copyright
 *
 * @link        http://www.webjump.com.br
 */

namespace PayPalBR\PayPalPlus\Model\Ui\PayPalPlus;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;

final class PayPalPlusConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCode = \PayPalBR\PayPalPlus\Model\PayPalPlus::PAYMENT_METHOD_PAYPALPLUS_CODE;

    /**
     * Contains the configuration path for showing exibition name
     */
    const XML_CUSTOMER_EXHIBITION_SHOW = 'payment/paypalbr_paypalplus/exhibition_name';


    /**
     * Contains the current mode, sandbox or production (live)
     */
    const XML_PATH_MODE = 'payment/paypalbr_paypalplus/mode';

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $_scopeConfig;

    /**
     * @param ConfigInterface $payPalPlusConfig
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        UrlInterface $urlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->urlBuilder = $urlBuilder;
        $this->_scopeConfig = $scopeConfig;
    }

    public function getConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $exibition = $this->_scopeConfig->getValue(self::XML_CUSTOMER_EXHIBITION_SHOW, $storeScope);
        $mode = $this->_scopeConfig->getValue(self::XML_PATH_MODE, $storeScope);

        if(empty($exibition)){
            $exibition = "";
        }

        return [
            'payment' => [
                $this->methodCode => [
                    'text' => 'payment/paypalbr_paypalplus/text',
                    'exibitionName' => $exibition,
                    'mode' => $mode
                ]
            ]
        ];
    }
}

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
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ConfigInterface $payPalPlusConfig
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        UrlInterface $urlBuilder
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->urlBuilder = $urlBuilder;
    }

    public function getConfig()
    {
        return [
            'payment' => [
                $this->methodCode => [
                    'text' => 'payment/paypalbr_paypalplus/text'
                ]
            ]
        ];
    }
}

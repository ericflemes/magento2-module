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
use PayPalBR\PayPalPlus\Gateway\Transaction\PayPalPlus\Config\ConfigInterface;

final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'paypalbr_paypalplus';

    protected $payPalPlusConfig;

    /**
     * @param ConfigInterface $payPalPlusConfig
     */
    public function __construct(
        ConfigInterface $payPalPlusConfig
    )
    {
        $this->setpayPalPlusConfig($payPalPlusConfig);
    }

    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'text' => $this->getpayPalPlusConfig()->getText()
                ]
            ]
        ];
    }

    /**
     * @return ConfigInterface
     */
    protected function getpayPalPlusConfig()
    {
        return $this->payPalPlusConfig;
    }

    /**
     * @param ConfigInterface $payPalPlusConfig
     * @return $this
     */
    protected function setpayPalPlusConfig(ConfigInterface $payPalPlusConfig)
    {
        $this->payPalPlusConfig = $payPalPlusConfig;
        return $this;
    }
}

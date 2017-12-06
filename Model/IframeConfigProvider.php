<?php


namespace PayPalBR\PayPalPlus\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;

class IframeConfigProvider implements ConfigProviderInterface
{
    const XML_PATH_SAVE_CARDS_TOKEN    = 'payment/paypalbr_paypalplus/save_cards_token';
    const XML_PATH_SAVE_STATUS_PENDING = 'payment/paypalbr_paypalplus/status_pending';
    const XML_PATH_EXPERIENCE_ID       = 'payment/paypalbr_paypalplus/profile_experience_id';
    const XML_PATH_ALLOW_SPECIFIC      = 'payment/paypalbr_paypalplus/allowspecific';
    const XML_PATH_SPECIFIC_COUNTRY    = 'payment/paypalbr_paypalplus/specificcountry';
    const XML_PATH_MIN_ORDER_TOTAL     = 'payment/paypalbr_paypalplus/min_order_total';
    const XML_PATH_INSTALLMENTS        = 'payment/paypalbr_paypalplus/installments';
    const XML_PATH_INSTALLMENTS_MONTHS = 'payment/paypalbr_paypalplus/installments_months';
    const XML_PATH_IFRAME_HEIGHT       = 'payment/paypalbr_paypalplus/iframe_height';
    const XML_PATH_IFRAME_LANGUAGE     = 'general/locale/code';
    const XML_PATH_SANDBOX_MODE        = 'payment/paypalbr_paypalplus/sandbox_flag';
    const IFRAME_CONFIG_CODE_NAME      = 'paypalPlusIframe';
    /**
     * @var string[]
     */
    protected $code = 'paypalbr_paypalplus';

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    protected $methods = [];

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var Connection
     */
    protected $_quote;
    protected $_logger;
    protected $_objectManager;
    protected $_localeResolver;
    /**
     *
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_httpConnection;

    /**
     * @param PaymentHelper $paymentHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Cart $cart,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_quote = $cart->getQuote();
        $this->_logger = $logger;
        $this->_objectManager = $objectManager;
        $this->_localeResolver = $localeResolver;
    }
    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'paypalPlusIframe' => [],
            ],
        ];
        $config['payment'][self::IFRAME_CONFIG_CODE_NAME]['config']['save_cards_token']      = $this->getStoreConfig(self::XML_PATH_SAVE_CARDS_TOKEN);
        $config['payment'][self::IFRAME_CONFIG_CODE_NAME]['config']['allowspecific']         = $this->getStoreConfig(self::XML_PATH_ALLOW_SPECIFIC);
        $config['payment'][self::IFRAME_CONFIG_CODE_NAME]['config']['specificcountry']       = $this->getStoreConfig(self::XML_PATH_SPECIFIC_COUNTRY);
        $config['payment'][self::IFRAME_CONFIG_CODE_NAME]['config']['min_order_total']       = $this->getStoreConfig(self::XML_PATH_MIN_ORDER_TOTAL);
        $config['payment'][self::IFRAME_CONFIG_CODE_NAME]['config']['status_pending']        = $this->getStoreConfig(self::XML_PATH_SAVE_STATUS_PENDING);
        $config['payment'][self::IFRAME_CONFIG_CODE_NAME]['config']['profile_experience_id'] = $this->getStoreConfig(self::XML_PATH_EXPERIENCE_ID);
        $config['payment'][self::IFRAME_CONFIG_CODE_NAME]['config']['installments']          = $this->getStoreConfig(self::XML_PATH_INSTALLMENTS);
        $config['payment'][self::IFRAME_CONFIG_CODE_NAME]['config']['installments_months']   = $this->getStoreConfig(self::XML_PATH_INSTALLMENTS_MONTHS);
        $config['payment'][self::IFRAME_CONFIG_CODE_NAME]['config']['iframeHeight']          = $this->getStoreConfig(self::XML_PATH_IFRAME_HEIGHT);
        $config['payment'][self::IFRAME_CONFIG_CODE_NAME]['config']['iframeLanguage']        = $this->getStoreConfig(self::XML_PATH_IFRAME_LANGUAGE);
        $config['payment'][self::IFRAME_CONFIG_CODE_NAME]['config']['isSandbox']             = $this->getStoreConfig(self::XML_PATH_SANDBOX_MODE);

        return $config;
    }
    /**
     * Get payment store config
     * @return string
     */
    public function getStoreConfig($configPath)
    {
        $value =  $this->scopeConfig->getValue(
                $configPath,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $value;
    }

}

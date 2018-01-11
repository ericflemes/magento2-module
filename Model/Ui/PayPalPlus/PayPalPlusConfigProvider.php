<?php

namespace PayPalBR\PayPal\Model\Ui\PayPalPlus;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Customer\Model\SessionFactory;

final class PayPalPlusConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCode = \PayPalBR\PayPal\Model\PayPalPlus::PAYMENT_METHOD_PAYPALPLUS_CODE;

    /**
     * Contains the configuration path for showing exibition name
     */
    const XML_CUSTOMER_EXHIBITION_SHOW = 'payment/paypalbr_paypalplus/exhibition_name';


    /**
     * Contains the current mode, sandbox or production (live)
     */
    const XML_PATH_MODE = 'payment/paypalbr_paypalplus/mode';

    /**
     * Contains the current mode, sandbox or production (live)
     */
    const XML_PATH_ACTIVE = 'payment/paypalbr_paypalplus/active';

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
    * @var \Magento\Customer\Model\SessionFactory
    */
    protected $sessionFactory;

    /**
     * @param ConfigInterface $payPalPlusConfig
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        UrlInterface $urlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        SessionFactory $sessionFactory
    ) {
        $this->sessionFactory = $sessionFactory;
        $this->paymentHelper = $paymentHelper;
        $this->urlBuilder = $urlBuilder;
        $this->_scopeConfig = $scopeConfig;
    }

    public function getConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $exibition = $this->_scopeConfig->getValue(self::XML_CUSTOMER_EXHIBITION_SHOW, $storeScope);
        $mode = $this->_scopeConfig->getValue(self::XML_PATH_MODE, $storeScope);
        $active = $this->_scopeConfig->getValue(self::XML_PATH_ACTIVE, $storeScope);

        if(empty($exibition)){
            $exibition = "";
        }
        $customerSession = $this->sessionFactory->create();
        $rememberedCard = '';

        if ($customerSession->isLoggedIn()){
            $customer = $customerSession->getCustomer();
            $data = $customer->getData();

            if (isset($data['remembered_card'])) {
                $rememberedCard = $data['remembered_card'];
            }
        }


        return [
            'payment' => [
                $this->methodCode => [
                    'active' => $active,
                    'text' => 'payment/paypalbr_paypalplus/text',
                    'exibitionName' => $exibition,
                    'mode' => $mode,
                    'rememberedCard' => $rememberedCard
                ]
            ]
        ];
    }
}

<?php
namespace PayPalBR\PayPalPlus\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;


class DataAssign implements ObserverInterface
{
    /**
     * Contains the config provider for Paypal Plus
     *
     * @var \PayPalBR\PayPalPlus\Model\ConfigProvider
     */
    protected $configProvider;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    protected $configInterface;

    const XML_PATH_CLIENT_ID_SANDBOX = 'payment/paypalbr_paypalplus/client_id_sandbox';
    const XML_PATH_SECRET_ID_SANDBOX = 'payment/paypalbr_paypalplus/secret_id_sandbox';
    const XML_PATH_CLIENT_ID_PROD = 'payment/paypalbr_paypalplus/client_id_production';
    const XML_PATH_SECRET_ID_PROD = 'payment/paypalbr_paypalplus/secret_id_production';
    const XML_PATH_MODE = 'payment/paypalbr_paypalplus/mode';
    const XML_PATH_ACTIVE = 'payment/paypalbr_paypalplus/active';
    const XML_PATH_TAX = 'customer/address/taxvat_show';

    public function __construct(
        \PayPalBR\PayPalPlus\Model\ConfigProvider $configProvider,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Message\ManagerInterface $messageManager)
    {
        $this->configProvider = $configProvider;
        $this->_logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->configInterface = $configInterface;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Message\ManagerInterface
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (! $this->configProvider->isCustomerTaxRequired()) {
           $this->configProvider->deactivateModule();
            return $this->messageManager->addErrorMessage(__('Identificamos que a sua loja não possui suporte para CPF/CNPJ (TAXVAT). Para adicionar o suporte, acesse <<hyperlink>> e vÃ¡ em â€œLojas->ConfiguraÃ§Ãµes->Clientes->OpÃ§Ãµes de nome e endereÃ§o->Mostrar nÃºmero TAX/VAT.'));
        }

        $clientId = $this->configProvider->getClientId();
        $secretId = $this->configProvider->getSecretId();
        if ($this->configProvider->isModeSandbox()) {
            $uri = "https://api.sandbox.paypal.com/v1/oauth2/token";
        } else {
            $uri = "https://api.paypal.com/v1/oauth2/token";
        }

        try {
            $paypalConfig = [
                'http.headers.PayPal-Partner-Attribution-Id' => 'MagentoBrazil_Ecom_PPPlus2',
                'mode' => $this->configProvider->isModeSandbox() ? 'sandbox' : 'live',
                'log.LogEnabled' => true,
                'log.FileName' => '/var/www/magento2.2/var/log/paypalplus.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => true,
                'http.CURLOPT_SSLVERSION' => 'CURL_SSLVERSION_TLSv1_2'
            ];
            $oauth = new \PayPal\Auth\OAuthTokenCredential($clientId, $secretId);
            $oauth->getAccessToken($paypalConfig);
        } catch (\Exception $e) {
           $this->configProvider->deactivateModule();
           return  $this->messageManager->addErrorMessage(__('Credenciais de API incorretas, favor revisar.'));
        }
    }
}
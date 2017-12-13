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

    public function __construct(
        \PayPalBR\PayPalPlus\Model\ConfigProvider $configProvider,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->configProvider = $configProvider;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Message\ManagerInterface
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $disableModule = false;
        $disableMessage = "";
        if (! $this->configProvider->isCustomerTaxRequired()) {
            $disableModule = true;
            $disableMessage = __('Identificamos que a sua loja não possui suporte para CPF/CNPJ (TAXVAT). Para adicionar o suporte, acesse <<hyperlink>> e vÃ¡ em â€œLojas->ConfiguraÃ§Ãµes->Clientes->OpÃ§Ãµes de nome e endereÃ§o->Mostrar nÃºmero TAX/VAT.');
        }

        if (! $this->configProvider->isCurrencyBaseBRL()) {
            $disableModule = true;
            $disableMessage = __("Your base currency has to be BRL in order to activate this module.");
        }

        try {
            $clientId = $this->configProvider->getClientId();
            $secretId = $this->configProvider->getSecretId();

            $paypalConfig = [
                'http.headers.PayPal-Partner-Attribution-Id' => 'MagentoBrazil_Ecom_PPPlus2',
                'mode' => $this->configProvider->isModeSandbox() ? 'sandbox' : 'live',
                'log.LogEnabled' => true,
                'log.FileName' => BP . '/var/log/paypalplus.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => true,
                'http.CURLOPT_SSLVERSION' => 'CURL_SSLVERSION_TLSv1_2'
            ];
            $oauth = new \PayPal\Auth\OAuthTokenCredential($clientId, $secretId);
            $oauth->getAccessToken($paypalConfig);
        } catch (\Exception $e) {

            $disableModule = true;
            $disableMessage = __('Credenciais de API incorretas, favor revisar.');
        }

        if ($disableModule) {
            $this->configProvider->deactivateModule();
            return $this->messageManager->addErrorMessage($disableMessage);
        }
    }
}
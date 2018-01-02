<?php
namespace PayPalBR\PayPalPlus\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;


class DataAssign implements ObserverInterface
{
     const WEBHOOK_URL_ALREADY_EXISTS = 'WEBHOOK_URL_ALREADY_EXISTS';
    /**
     * Contains the config provider for Paypal Plus
     *
     * @var \PayPalBR\PayPalPlus\Model\ConfigProvider
     */
    protected $configProvider;
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

        /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    public function __construct(
        \PayPalBR\PayPalPlus\Model\ConfigProvider $configProvider,
        \Magento\Framework\Message\ManagerInterface $messageManager,
         \Magento\Store\Model\StoreManagerInterface $storeManager,
         \Magento\Framework\UrlInterface $urlBuilder
    )
    {
        $this->_storeManager = $storeManager;
        $this->configProvider = $configProvider;
        $this->messageManager = $messageManager;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Message\ManagerInterface
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $disableModule = false;
        $disableMessage = "";


        if($this->configProvider->isStoreFrontActive() == false ){
            $disableModule = true;

            $url = $this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/customer');

            $disableMessage = __('Identificamos que a sua loja não possui está ativo (TAXVAT) para usuários GUEST. Para adicionar o suporte, acesse <a href="%1">Aqui</a> ou vá em Clientes->Configurações de Clientes->Criar Nova Conta Para Clientes->Mostrar número VAT em frontend.' , 
                $url
            );
        }
        if(! $this->configProvider->isTelephoneSet()){
            $disableModule = true;
            $disableMessage = __('Identificamos que a sua loja não possui um telefone ativo, favor habilitar para ativar o módulo');
        }

        if( ! $this->configProvider->isCustomerTaxRequired() ){
            $disableModule = true;
            $url = $this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/customer');

            $disableMessage = __('Identificamos que a sua loja não possui suporte para CPF/CNPJ (TAXVAT). Para adicionar o suporte, acesse <a href="%1"> Aqui</a> e vá em Loja->Configurações->Clientes->Opções de nome e  endereço->Mostrar número TAX/VAT.', 
                $url
            );
        }

        if (! $this->configProvider->isCurrencyBaseBRL() ) {
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
            $this->configProvider->desactivateModule();
            return $this->messageManager->addError($disableMessage);
        }

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $clientId,
               $secretId
            )
        );

        try {
        $output = \PayPal\Api\Webhook::getAll($apiContext);
        } catch (Exception $e) {
            print_r("Error in list webhooks was: {$e->getMessage()}");
            die;
        }

        if(empty($output->webhooks)){

            $baseUrl = $this->_storeManager->getStore()->getBaseUrl('link', true) .'rest/default/V1/notifications/webhooks';

            $webhook = new \PayPal\Api\Webhook();
            $webhook->setUrl($baseUrl);

            $webhookEventTypes = array();
            $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
                '{
                     "name": "PAYMENT.SALE.COMPLETED"
                }'
            );
            $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
                '{
                    "name": "PAYMENT.SALE.DENIED"
                }'
            );
            $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
                '{
                    "name": "PAYMENT.SALE.PENDING"
                }'
            );
            $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
                '{
                    "name": "PAYMENT.SALE.REFUNDED"
                }'
            );
            $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
                '{
                    "name": "RISK.DISPUTE.CREATED"
                }'
            );
            $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
                '{
                    "name": "CUSTOMER.DISPUTE.CREATED"
                }'
            );

            $webhook->setEventTypes($webhookEventTypes);

            try {
                $output = $webhook->create($apiContext);
            } catch (\PayPal\Exception\PayPalConnectionException $ex) {
                if ($ex->getData()) {
                    $data = json_decode($ex->getData(), true);
                    if (isset($data['name']) && $data['name'] == self::WEBHOOK_URL_ALREADY_EXISTS) {
                        return true;
                    }
                }
                return false;
            } catch (Exception $ex) {
                die($ex);
            }

        }

    }
}
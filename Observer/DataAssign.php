<?php
namespace PayPalBR\PayPal\Observer;
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


    protected $_responseFactory;


    public function __construct(
        \PayPalBR\PayPal\Model\ConfigProvider $configProvider,
        \Magento\Framework\Message\ManagerInterface $messageManager,
         \Magento\Store\Model\StoreManagerInterface $storeManager,
         \Magento\Framework\UrlInterface $urlBuilder,
         \Magento\Framework\App\ResponseFactory $responseFactory,
         \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    )
    {
        $this->_storeManager = $storeManager;
        $this->configProvider = $configProvider;
        $this->messageManager = $messageManager;
        $this->_urlBuilder = $urlBuilder;
        $this->_responseFactory = $responseFactory;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Message\ManagerInterface
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $disableModule = false;
        $disableMessage = "";
        $url = $this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/customer');

        if(! $this->configProvider->isStoreFrontActive() && $this->configProvider->isActive()){
            $disableModule = true;
            $disableMessage = __("We have identified that your store does not have the active TAX / VAT feature. To add it's support, go to <a href='%1'> Here </a> or go to Customers-> Customer Settings-> Create New Customer Account-> Display VAT number in frontend." , 
                $url
            );
        }
        if(! $this->configProvider->isTelephoneSet() && $this->configProvider->isActive()){
            $disableModule = true;
            $disableMessage = __('We have identified that your store does not have an active phone, please enable to activate the module');
        }

        if( ! $this->configProvider->isCustomerTaxRequired() && $this->configProvider->isActive()){
            $disableModule = true;
            $disableMessage = __('We have identified that your store does not have support for CPF / CNPJ (TAXVAT). To add support, go to <a href="%1"> Here </a> and go to Shop-> Settings-> Clients-> Name and address options-> Show TAX / VAT number.', 
                $url
            );
        }

        if (! $this->configProvider->isCurrencyBaseBRL() && $this->configProvider->isActive()) {
            $disableModule = true;
            $disableMessage = __("Your base currency has to be BRL in order to activate this module.");
        }

        if ($disableModule) {

            $this->configProvider->desactivateModule();

            $this->messageManager->addError($disableMessage);

            $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
 
            foreach ($types as $type) {  
                $this->_cacheTypeList->cleanType($type);
            }
 
            foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }

            $this->_responseFactory->create()
                    ->setRedirect($url)
                    ->sendResponse();
            exit(0);
            return $this;
        }

        try {
            $clientId = $this->configProvider->getClientId();
            $secretId = $this->configProvider->getSecretId();
            
            $paypalConfig = [
                'http.headers.PayPal-Partner-Attribution-Id' => 'MagentoBrazil_Ecom_PPPlus2',
                'mode' => $this->configProvider->isModeSandbox()? 'sandbox' : 'live',
                'log.LogEnabled' => true,
                'log.FileName' => BP . '/var/log/paypalbr/paypalplus.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'http.CURLOPT_SSLVERSION' => 'CURL_SSLVERSION_TLSv1_2'
            ];


            $apiContext = new \PayPal\Rest\ApiContext(
                new \PayPal\Auth\OAuthTokenCredential(
                    $clientId,
                    $secretId
                )
            );

            $apiContext->setConfig($paypalConfig);

            $oauth = new \PayPal\Auth\OAuthTokenCredential($clientId, $secretId);
            $oauth->getAccessToken($paypalConfig);
        } catch (\Exception $e) {
            $disableModule = true;
            $disableMessage = __('Incorrect API credentials, please review it.');
        }

        if ($disableModule) {

            $this->configProvider->desactivateModule();
            $this->configProvider->desactivateClientId();
            $this->configProvider->desactivateSecretId();

            $this->messageManager->addError($disableMessage);

            $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
 
            foreach ($types as $type) {  
                $this->_cacheTypeList->cleanType($type);
            }
 
            foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }

            $url = $this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/payment');

            $this->_responseFactory->create()
                    ->setRedirect($url)
                    ->sendResponse();
            exit(0);
            return $this;
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
        $newWebhook = true;
        foreach ($output->webhooks as $webhook) {
            $baseUrl = $this->_storeManager->getStore()->getBaseUrl('link', true) .'rest/default/V1/notifications/webhooks';
            if ($webhook->url == $baseUrl) {
                $newWebhook = false;
                $this->configProvider->saveWebhookId($webhook->id);
            }
        }

        if($newWebhook){

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
                $this->configProvider->saveWebhookId($output->id);
            } catch (\PayPal\Exception\PayPalConnectionException $ex) {
                if ($ex->getData()) {
                    $data = json_decode($ex->getData(), true);
                    if (isset($data['name']) && $data['name'] == self::WEBHOOK_URL_ALREADY_EXISTS) {
                        return true;
                    }
                    if (isset($data['details']) && isset($data['details'][0]) && isset($data['details'][0]['field']) && $data['details'][0]['field'] == 'url') {
                        $disableMessage = $data['details'][0]['issue'] . '. Url must be contain https.';
                        $this->messageManager->addError(__($disableMessage));
                    }
                }
                return false;
            } catch (Exception $ex) {
                die($ex);
            }

        }

        return $this;
      
    }
}
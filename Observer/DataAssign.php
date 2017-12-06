<?php
namespace PayPalBR\PayPalPlus\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;


class DataAssignObserver implements ObserverInterface
{

    protected $_scopeConfig;

    protected $_configInterface;

    const XML_PATH_CLIENT_ID = 'payment/paypalbr_paypalplus/client_id_sandbox';
    const XML_PATH_SECRET_ID = 'payment/paypalbr_paypalplus/secret_id_sandbox';
    const XML_PATH_MODE = 'payment/paypalbr_paypalplus/mode';
    const XML_PATH_ACTIVE = 'payment/paypalbr_paypalplus/active';
    const XML_PATH_TAX = 'customer/address/taxvat_show';

    public function __construct(\Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Message\ManagerInterface $messageManager)
    {
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->_configInterface = $configInterface;
        $this->messageManager = $messageManager;
    }
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $clientId = $this->_scopeConfig->getValue(self::XML_PATH_CLIENT_ID, $storeScope);
        $secret = $this->_scopeConfig->getValue(self::XML_PATH_SECRET_ID, $storeScope);
        $active = $this->_scopeConfig->getValue(self::XML_PATH_ACTIVE, $storeScope);
        $mode = $this->_scopeConfig->getValue(self::XML_PATH_MODE, $storeScope);
        $tax = $this->_scopeConfig->getValue(self::XML_PATH_TAX, $storeScope);
        $mode = ($mode == 1) ? 'sandbox' : 'live';


        if($tax != "req"){
           $this->_configInterface->saveConfig('payment/paypalbr_paypalplus/active', 0, 'default', 0);
           return  $this->messageManager->addErrorMessage(__('Identificamos que a sua loja nÃ£o possui suporte para CPF/CNPJ (TAXVAT). Para adicionar o suporte, acesse <<hyperlink>> e vÃ¡ em â€œLojas->ConfiguraÃ§Ãµes->Clientes->OpÃ§Ãµes de nome e endereÃ§o->Mostrar nÃºmero TAX/VAT.'));
        }

        if($mode == "sandbox"){
           $uri = "https://api.sandbox.paypal.com/v1/oauth2/token";
        }else{
           $uri = "https://api.paypal.com/v1/oauth2/token";
        }

        try{
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $uri, [
                'headers' =>
                    [
                        'Accept' => 'application/json',
                        'Accept-Language' => 'en_US',
                       'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                'body' => 'grant_type=client_credentials',

                'auth' => [$clientId, $secret, 'basic']
            ]
        );

        $data = json_decode($response->getBody(), true);
        $access_token = $data['access_token'];
        $this->_configInterface->saveConfig('payment/paypalbr_paypalplus/active', $active, 'default', 0);

        }catch (\Exception $ex) {

           $this->_configInterface->saveConfig('payment/paypalbr_paypalplus/active', 0, 'default', 0);
           return  $this->messageManager->addErrorMessage(__('Credenciais de API incompletas, favor revisar.'));

        }

    }
}
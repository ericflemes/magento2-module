<?php
namespace PayPalBR\PayPalPlus\Model;

class ObtainAccessToken
{
    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $_scopeConfig;

    /**
    * @var \Magento\Checkout\Model\Cart
    */
    protected $_cart;

    /**
    * Recipient email config path
    */
    const XML_PATH_CLIENT_ID = 'payment/paypal_plus/client_id_sandbox';
    const XML_PATH_SECRET_ID = 'payment/paypal_plus/secret_id_sandbox';
    const XML_PATH_MODE = 'payment/paypal_plus/mode';

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_cart = $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function postAccessToken()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $mode = $this->_scopeConfig->getValue(self::XML_PATH_MODE, $storeScope);
        $mode = ($mode == 1) ? 'sandbox' : 'live';

        $config_id = $this->_scopeConfig->getValue(self::XML_PATH_CLIENT_ID, $storeScope);
        $secret_id = $this->_scopeConfig->getValue(self::XML_PATH_SECRET_ID, $storeScope);

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $config_id ,
                $secret_id
            )
        );

        $apiContext->setConfig(
            array(
              'http.headers.PayPal-Partner-Attribution-Id' => 'MagentoBrazil_Ecom_PPPlus2',
              'mode' => $mode,
              'log.LogEnabled' => true,
              'log.FileName' => '../var/log/paypalplus.log',
              'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
              'cache.enabled' => true,
              'http.CURLOPT_SSLVERSION' => 'CURL_SSLVERSION_TLSv1_2'
            )
        );

        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod('paypal');

        $item1 = new \PayPal\Api\Item();
        $item1->setName('My item 1')
            ->setDescription('My description...')
            ->setQuantity('1')
            ->setPrice('12.00')
           // ->setTax('0.01')
            ->setsku('asd123')
            ->setCurrency('BRL');


        $shippingAddress = new \PayPal\Api\ShippingAddress();
        $shippingAddress->setRecipientName("Enzo Silva")
            ->setLine1("4o andar")
            ->setLine2("Unidade #34")
            ->setCity("Itapevi")
            ->setCountryCode("BR")
            ->setPostalCode("01425000")
            ->setPhone("5511987654321")
            ->setState("SP");

        $details = new \PayPal\Api\Details();
        $details->setShipping(1.00)
           ->setSubtotal("12.00");

        $itemList = new \PayPal\Api\ItemList();
        $itemList->addItem($item1);
        $itemList->setShippingAddress($shippingAddress);

        $amount = new \PayPal\Api\Amount();
        $amount->setCurrency("BRL");
        $amount->setTotal("13.00");
        $amount->setDetails($details);


        $paymentOptions = new \PayPal\Api\PaymentOptions();
        $paymentOptions->setAllowedPaymentMethod("IMMEDIATE_PAY");

        $transaction = new \PayPal\Api\Transaction();
        $transaction->setDescription("Creating a payment");
        $transaction->setAmount($amount);
        $transaction->setItemList($itemList);
        $transaction->setPaymentOptions($paymentOptions);


        $baseUrl = 'http://paypal.dev';
        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl("$baseUrl/ExecutePayment.php?success=true")->setCancelUrl("$baseUrl/ExecutePayment.php?success=false");

        $payment = new \PayPal\Api\Payment();
        $payment->setIntent("Sale");
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->setTransactions(array($transaction));

        try {

            $payment->create($apiContext);
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {

            echo $ex->getCode();
            echo $ex->getData();
            echo "Exception: " . $ex->getMessage() . PHP_EOL;
            $err_data = json_decode($ex->getData(), true);
            print_r($err_data);
            die();
        }

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/paypalplus.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($payment);

        return $payment->getApprovalLink();
        $retorno =   array('aprovall_url' => $payment->getApprovalLink(),
                            'mode' =>$mode );
        return json_encode($retorno);


    }
}

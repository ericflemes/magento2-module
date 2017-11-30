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
     * @var \Magento\Customer\Model\Session
     */
    protected $_customer;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
    * Recipient email config path
    */
    const XML_PATH_CLIENT_ID = 'payment/paypal_plus/client_id_sandbox';
    const XML_PATH_SECRET_ID = 'payment/paypal_plus/secret_id_sandbox';
    const XML_PATH_MODE = 'payment/paypal_plus/mode';

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Model\Session $customer,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_cart = $cart;
        $this->_customer = $customer;
        $this->_customerFactory = $customerFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function postAccessToken()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $mode = $this->_scopeConfig->getValue(self::XML_PATH_MODE, $storeScope);
        $mode = ($mode == 1) ? 'sandbox' : 'live';

        $configId = $this->_scopeConfig->getValue(self::XML_PATH_CLIENT_ID, $storeScope);
        $secretId = $this->_scopeConfig->getValue(self::XML_PATH_SECRET_ID, $storeScope);

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $configId,
                $secretId
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

        $customerId = $this->_customer->getId();
        $customerSession = $this->_customerFactory->create()->load($customerId);
        $quote = $this->_cart->getQuote();
        $storeCurrency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod('paypal');

        $itemList = new \PayPal\Api\ItemList();
        $cartItems = $quote->getItems();
        foreach ($cartItems as $cartItem) {
            $item = new \PayPal\Api\Item();
            $item->setName($cartItem->getName())
                ->setDescription($cartItem->getDescription())
                ->setQuantity($cartItem->getQty())
                ->setPrice($cartItem->getPrice())
                ->setTax('0.01')
                ->setSku($cartItem->getSku())
                ->setCurrency($storeCurrency);

            $itemList->addItem($item);
        }

        $cartShippingAddress = $quote->getShippingAddress();
        $shippingAddress = new \PayPal\Api\ShippingAddress();
        $customerShippingAddress = $this->_customerFactory->create()->load($cartShippingAddress->getCustomerId());

        $shippingAddress->setRecipientName($customerShippingAddress->getName())
            ->setLine1($cartShippingAddress->getStreetLine(1))
            ->setLine2($cartShippingAddress->getStreetLine(2))
            ->setCity($cartShippingAddress->getCity())
            ->setCountryCode($cartShippingAddress->getCountryId())
            ->setPostalCode($cartShippingAddress->getPostcode())
            ->setPhone($cartShippingAddress->getTelephone())
            ->setState($cartShippingAddress->getRegion());

        $itemList->setShippingAddress($shippingAddress);

        $details = new \PayPal\Api\Details();
        $details->setShipping($cartShippingAddress->getShippingAmount())
           ->setSubtotal($quote->getSubtotal())
           ->setTax('0.01');

        $amount = new \PayPal\Api\Amount();
        $amount->setCurrency($storeCurrency);
        $amount->setTotal($quote->getGrandTotal());
        $amount->setDetails($details);

        $paymentOptions = new \PayPal\Api\PaymentOptions();
        $paymentOptions->setAllowedPaymentMethod("IMMEDIATE_PAY");

        $transaction = new \PayPal\Api\Transaction();
        $transaction->setDescription("Creating a payment");
        $transaction->setAmount($amount);
        $transaction->setItemList($itemList);
        $transaction->setPaymentOptions($paymentOptions);

        $baseUrl = "http://paypal.dev";
        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl("{$baseUrl}/ExecutePayment.php?success=true")
            ->setCancelUrl("{$baseUrl}/ExecutePayment.php?success=false");

        $payment = new \PayPal\Api\Payment();
        $payment->setIntent("Sale");
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->setTransactions(array($transaction));

        try {
            $payment->create($apiContext);
        } catch (\PayPal\Exception\PayPalConnectionException $e) {
            echo $e->getCode();
            echo $e->getData();
            echo "Exception: " . $e->getMessage() . PHP_EOL;
            $err_data = json_decode($e->getData(), true);
            print_r($err_data);
            die();
        }

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/paypalplus.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($payment);

        return $payment->getApprovalLink();
    }
}

<?php
namespace PayPalBR\PayPalPlus\Model;

class ObtainAccessToken
{

   /**
   * @var \Magento\Framework\App\Config\ScopeConfigInterface
   */
   protected $scopeConfig;

   /**
   * Recipient email config path
   */
   const XML_PATH_CLIENT_ID = 'payment/paypal_plus/client_id_sandbox';
   const XML_PATH_SECRET_ID = 'payment/paypal_plus/secret_id_sandbox';
   const XML_PATH_MODE = 'payment/paypal_plus/mode';

   public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
   {
      $this->scopeConfig = $scopeConfig;
   }
    /**
     * {@inheritdoc}
     */
    public function postAccessToken()
    {


      $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
      $mode =  $this->scopeConfig->getValue(self::XML_PATH_MODE, $storeScope);
      $mode = ($mode == 1) ? 'sandbox' : 'live';

      $config_id =  $this->scopeConfig->getValue(self::XML_PATH_CLIENT_ID, $storeScope);
      $secret_id =  $this->scopeConfig->getValue(self::XML_PATH_SECRET_ID, $storeScope);


        // if( $test_site == true ){
        // $sdkConfig = array(
        //     "mode" => "sandbox"
        // );
        // }else{
        //     $sdkConfig = array(
        //         "mode" => "live"
        //     );
        // }

        $apiContext =  new \PayPal\Rest\ApiContext(
             new \PayPal\Auth\OAuthTokenCredential(
                $config_id ,
                $secret_id
            )
        );


        $apiContext->setConfig(
            array(
                'mode' => $mode,
                'log.LogEnabled' => true,
                'log.FileName' => '../var/log/paypalplus.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => true,
                'http.CURLOPT_SSLVERSION' => 'CURL_SSLVERSION_TLSv1_2'
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
                //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
            )
        );

        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod('paypal');


        $item1 =  new \PayPal\Api\Item();
        $item1->setName('My Custom Description')
        ->setCurrency('GBP')
        ->setQuantity('1')
         ->setPrice('.5')
         ->setsku('hjjh55');


        $itemList = new \PayPal\Api\ItemList();
        $itemList->setItems(array($item1));


        $amount =  new \PayPal\Api\Amount();
        $amount->setCurrency("USD");
        $amount->setTotal("12");


        $transaction =  new \PayPal\Api\Transaction();
        $transaction->setDescription("creating a payment");
        $transaction->setAmount($amount);


        $baseUrl = 'http://paypal.dev';
        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl("$baseUrl/ExecutePayment.php?success=true")
        ->setCancelUrl("$baseUrl/ExecutePayment.php?success=false");


        $payment =  new \PayPal\Api\Payment();
        $payment->setIntent("sale");
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->setTransactions(array($transaction));


        try {

           $payment->create($apiContext);

        } catch (\PayPal\Exception\PPConnectionException $ex) {

                echo "Exception: " . $ex->getMessage() . PHP_EOL;
                $err_data = json_decode($ex->getData(), true);
                print_r($err_data);
                die();

        }

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/paypalplus.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($payment);
        return  $payment->getApprovalLink();



        die;



        $curl = curl_init();
        $data_string = '{
                                  "intent": "sale",
                                  "payer": {
                                  "payment_method": "paypal"
                                  },
                                  "transactions": [
                                  {
                                    "amount": {
                                    "total": "30.11",
                                    "currency": "USD",
                                    "details": {
                                      "subtotal": "30.00",
                                      "tax": "0.07",
                                      "shipping": "0.03",
                                      "handling_fee": "1.00",
                                      "shipping_discount": "-1.00",
                                      "insurance": "0.01"
                                    }
                                    },
                                    "description": "The payment transaction description.",
                                    "custom": "EBAY_EMS_90048630024435",
                                    "invoice_number": "48787589673",
                                    "payment_options": {
                                    "allowed_payment_method": "INSTANT_FUNDING_SOURCE"
                                    },
                                    "soft_descriptor": "ECHI5786786",
                                    "item_list": {
                                    "items": [
                                      {
                                      "name": "hat",
                                      "description": "Brown hat.",
                                      "quantity": "5",
                                      "price": "3",
                                      "tax": "0.01",
                                      "sku": "1",
                                      "currency": "USD"
                                      },
                                      {
                                      "name": "handbag",
                                      "description": "Black handbag.",
                                      "quantity": "1",
                                      "price": "15",
                                      "tax": "0.02",
                                      "sku": "product34",
                                      "currency": "USD"
                                      }
                                    ],
                                    "shipping_address": {
                                      "recipient_name": "Brian Robinson",
                                      "line1": "4th Floor",
                                      "line2": "Unit #34",
                                      "city": "San Jose",
                                      "country_code": "US",
                                      "postal_code": "95131",
                                      "phone": "011862212345678",
                                      "state": "CA"
                                    }
                                    }
                                  }
                                  ],
                                  "note_to_payer": "Contact us for any questions on your order.",
                                  "redirect_urls": {
                                  "return_url": "http://www.paypal.com/return",
                                  "cancel_url": "http://www.paypal.com/cancel"
                                  }
                                }';

          curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.sandbox.paypal.com/v1/payments/payment ",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $data_string,
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer $cred->accessToken",
            "content-type: application/json"
          ),
        ));

        return $url;
    }
}

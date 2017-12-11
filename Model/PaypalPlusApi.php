<?php
namespace PayPalBR\PayPalPlus\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class PaypalPlusApi
 *
 * TODO: We need to change the name of this class, I can't think of any right now
 *
 * @package PayPalBR\PayPalPlus\Model
 */
class PaypalPlusApi
{

    /**
     * Contains the cart of current session
     *
    * @var \Magento\Checkout\Model\Cart
    */
    protected $cart;

    /**
     * Contains the current customer session
     * 
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Contains the store manager of Magento
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Contains the config provider for Magento 2 back-end configurations
     *
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * Contains the quote object for payment
     *
     * @var \Magento\Payment\Model\Cart\SalesModel\Quote
     */
    protected $cartSalesModelQuote;

    /**
     * Contains the config ID to be used in PayPal API
     *
     * @var string
     */
    protected $configId;

    /**
     * Contains the secret ID to be used in PayPal API
     *
     * @var string
     */
    protected $secretId;

    /**
     * PaypalPlusApi constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \PayPalBR\PayPalPlus\Model\ConfigProvider $configProvider,
        \Magento\Payment\Model\Cart\SalesModel\Factory $cartSalesModelFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->cart = $cart;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->configProvider = $configProvider;

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $cart->getQuote();
        $this->cartSalesModelQuote = $cartSalesModelFactory->create($quote);
    }

    /**
     * Builds and returns the api context to be used in PayPal Plus API
     *
     * @return \PayPal\Rest\ApiContext
     */
    protected function getApiContext()
    {
        $this->configId = $this->configProvider->getClientId();
        $this->secretId = $this->configProvider->getSecretId();

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $this->configId,
                $this->secretId
            )
        );

        return $apiContext;
    }

    /**
     * Returns the payer
     *
     * @return \PayPal\Api\Payer
     */
    protected function getPayer()
    {
        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod('paypal');
        return $payer;
    }

    /**
     * Returns redirect urls
     *
     * These URLs are defined in the Mexico project
     *
     * @return \PayPal\Api\RedirectUrls
     */
    protected function getRedirectUrls()
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();

        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls
            ->setReturnUrl($store->getUrl('checkout/cart'))
            ->setCancelUrl($store->getUrl('checkout/cart'));
        return $redirectUrls;
    }

    /**
     * Returns shipping addresss for PayPalPlus
     *
     * @return \PayPal\Api\ShippingAddress
     */
    protected function getShippingAddress()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cart->getQuote();
        $cartShippingAddress = $quote->getShippingAddress();
        $customer = $this->customerSession->getCustomer();

        $shippingAddress = new \PayPal\Api\ShippingAddress();
        $shippingAddress
            ->setRecipientName($customer->getName())
            ->setLine1($cartShippingAddress->getStreetLine(1))
            ->setLine2($cartShippingAddress->getStreetLine(2))
            ->setCity($cartShippingAddress->getCity())
            ->setCountryCode($cartShippingAddress->getCountryId())
            ->setPostalCode($cartShippingAddress->getPostcode())
            ->setPhone($cartShippingAddress->getTelephone())
            ->setState($cartShippingAddress->getRegion());
        return $shippingAddress;
    }

    /**
     * Returns the items in the cart
     *
     * @return \PayPal\Api\ItemList
     */
    protected function getItemList()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cart->getQuote();

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();

        /** @var string $storeCurrency */
        $storeCurrency = $quote->getBaseCurrencyCode();

        $itemList = new \PayPal\Api\ItemList();
        $cartItems = $quote->getItems();
        foreach ($cartItems as $cartItem) {
            $item = new \PayPal\Api\Item();
            $item->setName($cartItem->getName())
                ->setDescription($cartItem->getDescription())
                ->setQuantity($cartItem->getQty())
                ->setPrice($cartItem->getPrice())
                ->setSku($cartItem->getSku())
                ->setCurrency($storeCurrency);
            $itemList->addItem($item);
        }

        $shippingAddress = $this->getShippingAddress();
        $itemList->setShippingAddress($shippingAddress);
        return $itemList;
    }

    /**
     * Returns details for PayPal Plus API
     *
     * @return \PayPal\Api\Details
     */
    protected function getDetails()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cart->getQuote();

        /**
         * If subtotal + shipping + tax not equals grand total,
         * then a disscount might be applying, get subtotal with disscount then.
         */
        $baseSubtotal =  $this->cartSalesModelQuote->getBaseSubtotal() + $this->cartSalesModelQuote->getBaseDiscountAmount();

        if ($quote->getBaseGiftCardsAmount()) {
            $baseSubtotal -= $quote->getBaseGiftCardsAmount();
        }

        if ($quote->getBaseCustomerBalAmountUsed()) {
            $baseSubtotal -= $this->_quote->getBaseCustomerBalAmountUsed();
        }


        $details = new \PayPal\Api\Details();
        $details
            ->setShipping($this->cartSalesModelQuote->getBaseShippingAmount())
            ->setSubtotal($baseSubtotal);
        return $details;
    }

    /**
     * Returns amount PayPal Plus API
     *
     * @return \PayPal\Api\Amount
     */
    protected function getAmount()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cart->getQuote();
        $storeCurrency = $quote->getBaseCurrencyCode();
        $grandTotal = $quote->getGrandTotal();
        $details = $this->getDetails();

        $amount = new \PayPal\Api\Amount();
        $amount->setCurrency($storeCurrency);
        $amount->setTotal($grandTotal);
        $amount->setDetails($details);

        return $amount;
    }

    /**
     * Return transaction object for PayPalPlus API
     *
     * @return \PayPal\Api\Transaction
     */
    protected function getTransaction()
    {
        $amount = $this->getAmount();
        $itemList = $this->getItemList();

        $paymentOptions = new \PayPal\Api\PaymentOptions();
        $paymentOptions->setAllowedPaymentMethod("IMMEDIATE_PAY");

        $transaction = new \PayPal\Api\Transaction();
        $transaction->setDescription("Creating a payment");
        $transaction->setAmount($amount);
        $transaction->setItemList($itemList);
        $transaction->setPaymentOptions($paymentOptions);
        return $transaction;
    }

    public function execute()
    {
        $apiContext = $this->getApiContext();
        $payer = $this->getPayer();
        $redirectUrls = $this->getRedirectUrls();
        $transaction = $this->getTransaction();

        $payment = new \PayPal\Api\Payment();
        $payment->setIntent("Sale");
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->addTransaction($transaction);

        try {
            /** @var \PayPal\Api\Payment $result */
            $result = $payment->create($apiContext);
            echo "<pre>";
            echo $result->toJSON();
            exit;
        } catch (\PayPal\Exception\PayPalConnectionException $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}

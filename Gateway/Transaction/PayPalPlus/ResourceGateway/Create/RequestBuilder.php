<?php
/**
 * Class RequestBuilder
 *
 * @author      Webjump Core Team <dev@webjump.com>
 * @copyright   2016 Webjump (http://www.webjump.com.br)
 * @license     http://www.webjump.com.br Copyright
 *
 * @link        http://www.webjump.com.br
 */

namespace PayPalBR\PayPalPlus\Gateway\Transaction\PayPalPlus\ResourceGateway\Create;

use function Couchbase\defaultDecoder;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Item;
use Magento\Checkout\Model\Cart;
use PayPalBR\PayPalPlus\Gateway\Transaction\Base\Config\Config;

class RequestBuilder implements BuilderInterface
{
    const MODULE_NAME = 'PayPalBR_PayPalPlus';

    protected $requestDataProviderFactory;
    protected $cartItemRequestDataProviderFactory;
    protected $orderAdapter;
    protected $cart;
    protected $config;

    /**
     * RequestBuilder constructor.
     * @param Cart $cart
     * @param Config $config
     * @param ModuleHelper $moduleHelper
     */
    public function __construct(
        Cart $cart,
        Config $config
    ) {
        $this->setCart($cart);
        $this->setConfig($config);
    }

    protected $paymentData;

    /**
     * {@inheritdoc}
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDataObject */
        $paymentDataObject = $buildSubject['payment'];
        $this->setOrderAdapter($paymentDataObject->getOrder());

        $this->setPaymentData($paymentDataObject->getPayment());

        $requestDataProvider = $this->createRequestDataProvider();

        return $this->createNewRequest($requestDataProvider);

    }

    /**
     * @param Request $request
     * @return $this
     */
    protected function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * @return BilletRequestDataProviderInterface
     */
    protected function createRequestDataProvider()
    {
        return $this->getRequestDataProviderFactory()->create([
            'orderAdapter' => $this->getOrderAdapter(),
            'payment' => $this->getPaymentData()
        ]);
    }

    /**
     * @param Item $item
     * @return CartItemRequestDataProviderInterface
     */
    protected function createCartItemRequestDataProvider(Item $item)
    {
        return $this->getCartItemRequestProviderFactory()->create([
            'item' => $item
        ]);
    }

    /**
     * @return BoletoTransaction
     */
    protected function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param BoletoTransaction $transaction
     * @return RequestBuilder
     */
    protected function setTransaction(BoletoTransaction $transaction)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * @return BilletRequestDataProviderInterfaceFactory
     */
    protected function getRequestDataProviderFactory()
    {
        return $this->requestDataProviderFactory;
    }

    /**
     * @param BilletRequestDataProviderInterfaceFactory $requestDataProviderFactory
     * @return self13
     */
    protected function setRequestDataProviderFactory(BilletRequestDataProviderInterfaceFactory $requestDataProviderFactory)
    {
        $this->requestDataProviderFactory = $requestDataProviderFactory;
        return $this;
    }

    /**
     * @return CartItemRequestDataProviderInterfaceFactory
     */
    protected function getCartItemRequestProviderFactory()
    {
        return $this->cartItemRequestDataProviderFactory;
    }

    /**
     * @param CartItemRequestDataProviderInterfaceFactory $cartItemRequestDataProviderFactory
     * @return self
     */
    protected function setCartItemRequestProviderFactory(CartItemRequestDataProviderInterfaceFactory $cartItemRequestDataProviderFactory)
    {
        $this->cartItemRequestDataProviderFactory = $cartItemRequestDataProviderFactory;
        return $this;
    }


    /**
     * @return OrderAdapterInterface
     */
    protected function getOrderAdapter()
    {
        return $this->orderAdapter;
    }

    /**
     * @param OrderAdapterInterface $orderAdapter
     * @return $this
     */
    protected function setOrderAdapter(OrderAdapterInterface $orderAdapter)
    {
        $this->orderAdapter = $orderAdapter;
        return $this;
    }

    /**
     * @return InfoInterface
     */
    public function getPaymentData()
    {
        return $this->paymentData;
    }

    public function calcBoletoDays($days)
    {

        $pattern = 'T00:00:00Z';

        return date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $days . ' days')) . $pattern;

    }

    /**
     * @param InfoInterface $paymentData
     * @return $this
     */
    protected function setPaymentData(InfoInterface $paymentData)
    {
        $this->paymentData = $paymentData;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param $cart
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;

    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return \MundiAPILib\MundiAPIClient
     */
    public function getApi()
    {
        return new \MundiAPILib\MundiAPIClient($this->getConfig()->getSecretKey(), '');
    }

    /**
     * @return \MundiAPILib\Models\CreateOrderRequest
     */
    public function getOrderRequest()
    {
        return new \MundiAPILib\Models\CreateOrderRequest();
    }

    /**
     * @return mixed
     */
    public function setConfigCreditCard($configCreditCard)
    {
        $this->configCreditCard = $configCreditCard;

        return $this;
    }
    /**
     * @return mixed
     */
    public function getModuleHelper()
    {
        return $this->moduleHelper;
    }

    /**
     * @return mixed
     */
    public function setModuleHelper($moduleHelper)
    {
        $this->moduleHelper = $moduleHelper;

        return $this;
    }


    public function getBankNumber($title){

        switch ($title){
            case 'Itau':
                return '341';
                break;

            case 'Bradesco':
                return '237';
                break;

            case 'Santander':
                return '033';
                break;

            case 'CitiBank':
                return '745';
                break;

            case 'BancoDoBrasil':
                return '001';
                break;

            case 'Caixa':
                return '104';
                break;

            default:
                return false;

        }

    }

    /**
     * @param $requestDataProvider
     * @return mixed
     */
    protected function createNewRequest($requestDataProvider)
    {

        $quote = $this->getCart()->getQuote();
        $order = $this->getOrderRequest();

        $order->payments = [
            [
                'amount' => $quote->getGrandTotal() * 100,
                'payment_method' => 'boleto',
                'capture' => true,
                'boleto' => [
                    'bank' => $this->getBankNumber($requestDataProvider->getBankType()),
                    'instructions' => $requestDataProvider->getInstructions(),
                    'due_at' => $this->calcBoletoDays($requestDataProvider->getDaysToAddInBoletoExpirationDate())
                ]
            ]
        ];

        $order->items = [];

        foreach ($requestDataProvider->getCartItems() as $key => $item) {

            $cartItemDataProvider = $this->createCartItemRequestDataProvider($item);

            $itemValues = [
                'amount' => $cartItemDataProvider->getUnitCostInCents(),
                'description' => $cartItemDataProvider->getName(),
                'quantity' => $cartItemDataProvider->getQuantity()
            ];
            array_push($order->items, $itemValues);

        }

        $order->customer = [
            'name' => !empty($requestDataProvider->getName()) ? $requestDataProvider->getName() :  $quote->getBillingAddress()->getFirstName() . ' ' . $quote->getBillingAddress()->getLastName(),
            'email' => !empty($requestDataProvider->getEmail()) ? $requestDataProvider->getEmail() : $quote->getBillingAddress()->getEmail(),
            'address' => [
                'street' => $quote->getShippingAddress()->getStreetLine(1),
                'number' => $quote->getShippingAddress()->getStreetLine(2),
                'zip_code' => trim(str_replace('-','',$quote->getShippingAddress()->getPostCode())),
                'neighborhood' => $quote->getShippingAddress()->getStreetLine(4),
                'city' => $quote->getShippingAddress()->getCity(),
                'state' => $quote->getShippingAddress()->getRegionCode(),
                'country' => $quote->getShippingAddress()->getCountryId()
            ]
        ];

        $order->ip = $requestDataProvider->getIpAddress();

        $order->shipping = [
            'amount' => $quote->getShippingAddress()->getShippingAmount() * 100,
            'description' => '.',
            'address' => [
                'street' => $quote->getShippingAddress()->getStreetLine(1),
                'number' => $quote->getShippingAddress()->getStreetLine(2) . ' ' . $quote->getShippingAddress()->getStreetLine(3),
                'zip_code' => trim(str_replace('-','',$quote->getShippingAddress()->getPostCode())),
                'neighborhood' => $quote->getShippingAddress()->getStreetLine(4),
                'city' => $quote->getShippingAddress()->getCity(),
                'state' => $quote->getShippingAddress()->getRegionCode(),
                'country' => $quote->getShippingAddress()->getCountryId()
            ]
        ];

        $order->session_id = $requestDataProvider->getSessionId();

        $order->metadata = [
            'module_name' => self::MODULE_NAME,
            'module_version' => $this->getModuleHelper()->getVersion(self::MODULE_NAME),
        ];

        try {

            $response = $this->getApi()->getOrders()->createOrder($order);

        } catch (\MundiAPILib\Exceptions\ErrorException $error) {

            print "<pre>";
            print json_encode($error, JSON_PRETTY_PRINT);
            print "</pre>";

            return $error;

        } catch (\Exception $ex) {

            print "<pre>";
            print json_encode($ex, JSON_PRETTY_PRINT);
            print "</pre>";

            return $ex;
        }

        return $response;

    }

}

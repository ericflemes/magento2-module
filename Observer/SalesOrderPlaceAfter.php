<?php

namespace PayPalBR\PayPalPlus\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Service\OrderService;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

class SalesOrderPlaceAfter implements ObserverInterface
{



    /** session factory */
    protected $_session;

    /** @var CustomerFactoryInterface */
    protected $_customerFactory;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * \Magento\Sales\Model\Service\OrderService
     */
    protected $orderService;

    /**
     * \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param Api $api
     */
    public function __construct(
        Session $checkoutSession,
        LoggerInterface $logger,
        OrderService $orderService,
        CustomerFactory $customerFactory,
        SessionFactory $sessionFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->setCheckoutSession($checkoutSession);
        $this->setLogger($logger);
        $this->setOrderService($orderService);
        $this->_customerFactory = $customerFactory;
        $this->sessionFactory = $sessionFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $payment = $order->getPayment();

        $status = $payment->getAdditionalInformation('state_payPal');
        $r_card = $payment->getAdditionalInformation('remebered_card');

        $customerSession = $this->sessionFactory->create();
        if ($customerSession->isLoggedIn()) 
        {
            $customer = $customerSession->getCustomer();
            $customer = $this->customerRepository->getById($customer->getId());
            $customer->getCustomAttribute('remebered_card');
            $customer->setCustomAttribute('remebered_card',$r_card);
            try {                
                $customer = $this->customerRepository->save($customer);
            }catch (Exception $e) {
                return $e->getMessage();
            }
        }

        if ($order->canCancel() && $status == 'failed') {
            $result = $this->cancelOrder($order);
            $this->logger($result);
        }
    }

    /**
     * @param Order $order
     * @return $cancel
     */
    protected function cancelOrder($order)
    {
        $cancel = $this->getOrderService()->cancel($order->getId());

        return $cancel;
    }

    /**
     * @param mixed $data
     */
    protected function logger($data){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/paypal-SalesOrderPlaceAfter-' . date('Y-m-d') . '.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Debug Initial SalesOrderPlaceAfter');
        $logger->info($data);
        $logger->info('Debug Final SalesOrderPlaceAfter');
    }

    /**
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckoutSession()
    {
        return $this->checkoutSession;
    }

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     *
     * @return self
     */
    public function setCheckoutSession(\Magento\Checkout\Model\Session $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;

        return $this;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return self
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderService()
    {
        return $this->orderService;
    }

    /**
     * @param mixed $orderService
     *
     * @return self
     */
    public function setOrderService($orderService)
    {
        $this->orderService = $orderService;

        return $this;
    }
}

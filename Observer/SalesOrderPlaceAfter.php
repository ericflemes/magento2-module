<?php

namespace PayPalBR\PayPalPlus\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Service\OrderService;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Framework\DB\Transaction;

class SalesOrderPlaceAfter implements ObserverInterface
{
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
     * \Magento\Sales\Model\Service\InvoiceService
     */
    protected $invoiceService;

    /**
     * \Magento\Framework\DB\Transaction
     */
    protected $transaction;

    /**
     * \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param Api $api
     */
    public function __construct(
        Session $checkoutSession,
        LoggerInterface $logger,
        OrderService $orderService,
        InvoiceService $invoiceService,
        Transaction $transaction,
        InvoiceSender $invoiceSender
    ) {
        $this->setCheckoutSession($checkoutSession);
        $this->setLogger($logger);
        $this->setOrderService($orderService);
        $this->setTransaction($transaction);
        $this->setInvoiceService($invoiceService);
        $this->setInvoiceSender($invoiceSender);
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

        if ($order->canCancel() && $status == 'failed') {
            $result = $this->cancelOrder($order);
            $this->logger($result);
        }

        if ($order->canInvoice() && $status == 'approved') {
            $result = $this->createInvoice($order);
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
     * @param Order $order
     * @return $invoice
     */
    protected function createInvoice($order)
    {
        $invoice = $this->getInvoiceService()->prepareInvoice($order);
        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
        $invoice->register();
        $invoice->save();
        $transactionSave = $this->getTransaction()->addObject(
            $invoice
        )->addObject(
            $invoice->getOrder()
        );
        $transactionSave->save();
        $this->getInvoiceSender()->send($invoice);

        $order->addStatusHistoryComment(
            __('Notified customer about invoice #%1.', $invoice->getIncrementId())
        )
        ->setIsCustomerNotified(true)
        ->save();

        $order->setState('processing')->setStatus('processing');
        $order->save();

        return $invoice->getData();
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

    /**
     * @return mixed
     */
    public function getInvoiceService()
    {
        return $this->invoiceService;
    }

    /**
     * @param mixed $invoiceService
     *
     * @return self
     */
    public function setInvoiceService($invoiceService)
    {
        $this->invoiceService = $invoiceService;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param mixed $transaction
     *
     * @return self
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInvoiceSender()
    {
        return $this->invoiceSender;
    }

    /**
     * @param mixed $invoiceSender
     *
     * @return self
     */
    public function setInvoiceSender($invoiceSender)
    {
        $this->invoiceSender = $invoiceSender;

        return $this;
    }
}

<?php
/**

 * @author Diego Lisboa <diego@webjump.com.br>
 * @category PayPalBR
 * @package paypalbr\PayPalPlus\
 * @copyright   WebJump (http://www.webjump.com.br)
 *
 * © 2016 WEB JUMP SOLUTIONS
 *
 */
namespace PayPalBR\PayPalPlus\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use PayPalBR\PayPalPlus\Model\Http\Api;

/**
 * PayPalPlus module observer
 */
class clearPaymentDataAfterSaveOrder implements ObserverInterface
{
    /**
     * Contains the checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Contains the logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param Api $api
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    /**
     * Clear payment data only after order is saved.
     * This is because if any error occures while saving the order after payment is executed,
     * the user will try to reorder, and if payment data is cleared before this, user would be charged twice.
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $event = $observer->getEvent();

        /** @var $order \Magento\Sales\Model\Order */
        $order = $event->getOrder();

        if (
            $order &&
            $order->getId() &&
            $order->getPayment()->getMethod() == \PayPalBR\PayPalPlus\Model\Payment\PayPalPlus::METHOD_NAME
        ) {
            $this->checkoutSession->setPaymentId(false);
            $this->checkoutSession->setIframeUrl(false);
            $this->checkoutSession->setExecuteUrl(false);
            $this->checkoutSession->setPaymentIdExpires(false);
            $this->checkoutSession->setPaypalPaymentId( null );
            $this->checkoutSession->setQuoteUpdatedAt( null );
        }
    }
}

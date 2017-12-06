<?php
/**

 * @author Diego Lisboa <diego@webjump.com.br>
 * @category PayPalBR
 * @package paypalbr\PayPalPlus\
 * @copyright   qbo (http://www.webjump.com.br)
 *
 * © 2016 WEB JUMP SOLUTIONS
 *
 */
namespace PayPalBR\PayPalPlus\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use qbo\PayPalPlusMx\Model\Http\Api;

/**
 * PayPalPlus module observer
 */
class clearPaymentDataAfterSaveOrder implements ObserverInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     *
     * @var qbo\PayPalPlusMx\Model\Http\Api
     */
    protected $_api;
    /**
     * @var string
     */
    const METHOD_CODE = 'paylpalbr_paypalplus';

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param Api $api
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        Api $api
    ) {
        $this->_logger = $logger;
        $this->_api = $api;
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
        /* @var $order \Magento\Sales\Model\Order */
        $order = $event->getOrder();

        if ($order && $order->getId() && $order->getPayment()->getMethod() == self::METHOD_CODE) {
            $this->_api->clearPaymentData();
        }
    }
}

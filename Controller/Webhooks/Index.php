<?php

namespace PayPalBR\PayPalPlus\Controller\Webhooks;
use Magento\Framework\Exception\LocalizedException;


class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var \PayPalBR\PayPalPlus\Model\Webhook\EventFactory
     */
    protected $_webhookEventFactory;
    /**
     * @var \PayPalBR\PayPalPlus\Model\ApiFactory
     */
    protected $_apiFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \PayPalBR\PayPalPlus\Model\Webhook\EventFactory $webhookEventFactory
     * @param \PayPalBR\PayPalPlus\Model\ApiFactory $apiFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \PayPalBR\PayPalPlus\Model\Webhook\EventFactory $webhookEventFactory,
        \PayPalBR\PayPalPlus\Model\ApiFactory $apiFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_logger = $logger;
        $this->_webhookEventFactory = $webhookEventFactory;
        $this->_apiFactory = $apiFactory;
        parent::__construct($context);
    }
    /**
     * Instantiate Event model and pass Webhook request to it
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }
        try {
            $data = file_get_contents('php://input');
            /** @var \PayPal\Api\WebhookEvent $webhookEvent */
            $webhookEvent = $this->_apiFactory->create()->validateWebhook($data);
            if (!$webhookEvent) {
                throw new LocalizedException(__('Event not found.'));
            }
            $this->_webhookEventFactory->create()->processWebhookRequest($webhookEvent);
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            $this->getResponse()->setStatusHeader(503, '1.1', 'Service Unavailable')->sendResponse();
        }
    }
}
<?php
namespace PayPalBR\PayPalPlus\Controller\Webhooks;

use Magento\Framework\Exception\LocalizedException;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    /**
     * @var \PayPalBR\PayPalPlus\Model\Webhook\EventFactory
     */
    protected $webhookEventFactory;
    
    /**
     * @var \PayPalBR\PayPalPlus\Model\ApiFactory
     */
    protected $apiFactory;
    
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
        $this->logger = $logger;
        $this->webhookEventFactory = $webhookEventFactory;
        $this->apiFactory = $apiFactory;
        parent::__construct($context);
    }

    /**
     * Instantiate event model and pass Webhook request to it
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function execute()
    {
        if (! $this->getRequest()->isPost()) {
            return;
        }
        try {
            $data = file_get_contents('php://input');

            /** @var \PayPal\Api\WebhookEvent $webhookEvent */
            $webhookEvent = $this->apiFactory->create()->validateWebhook($data);
            if (!$webhookEvent) {
                throw new LocalizedException(__('Event not found.'));
            }
            $this->webhookEventFactory->create()->processWebhookRequest($webhookEvent);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->getResponse()->setStatusHeader(503, '1.1', 'Service Unavailable')->sendResponse();
        }
    }
}
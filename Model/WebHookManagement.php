<?php
namespace PayPalBR\PayPal\Model;

use oauth;
use PayPalBR\PayPal\Api\EventsInterface;
use PayPalBR\PayPal\Api\WebHookManagementInterface;
use PayPalBR\PayPal\Model\ConfigProvider;

class WebHookManagement implements WebHookManagementInterface
{
    protected $eventWebhook;
    protected $configProvider;

    public function __construct(
        EventsInterface $eventWebhook,
        ConfigProvider $configProvider
    ) {
        $this->setEventWebhook($eventWebhook);
        $this->setConfigProvider($configProvider);
    }

    /**
    * {@inheritdoc}
    */
    public function postWebHook($id, $create_time, $resource_type, $event_type, $summary, $resource, $links, $event_version)
    {

        if (! $id) {
            return false;
        }

        $webhookApi = new \PayPal\Api\WebhookEvent;

        $webhookApi
            ->setId($id)
            ->setCreateTime($create_time)
            ->setResourceType($resource_type)
            ->setEventType($event_type)
            ->setSummary($summary)
            ->setResource($resource)
            ->setLinks($links);
        try {
            $array = [
                'id' => $id,
                'create_time' => $create_time,
                'resource_type' => $resource_type,
                'event_type' => $event_type,
                'summary' => $summary,
                'resource' => $resource,
                'links' => $links,
                'event_version' => $event_version,
            ];
            if ($this->getConfigProvider()->isDebugEnabled()) {
                $this->logger($array);
            }
            $this->getEventWebhook()->processWebhookRequest($webhookApi);
            $return = [
                [
                    'status' => 200,
                    'message' => $summary
                ]
            ];
        } catch (\Exception $e) {
            $this->logger('initial debug');
            $this->logger($e);
            $this->logger('final debug');

            $return = [
                [
                    'status' => 400,
                    'message' => $e->getMessage()
                ]
            ];
        }

        return $return;
    }

    protected function logger($array)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/paypalbr/paypalplus-webhook.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($array);
    }

    /**
     * @return mixed
     */
    public function getEventWebhook()
    {
        return $this->eventWebhook;
    }

    /**
     * @param mixed $eventWebhook
     *
     * @return self
     */
    public function setEventWebhook($eventWebhook)
    {
        $this->eventWebhook = $eventWebhook;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfigProvider()
    {
        return $this->configProvider;
    }

    /**
     * @param mixed $configProvider
     *
     * @return self
     */
    public function setConfigProvider($configProvider)
    {
        $this->configProvider = $configProvider;

        return $this;
    }
}
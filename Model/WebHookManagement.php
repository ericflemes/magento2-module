<?php
namespace PayPalBR\PayPalPlus\Model;

use oauth;
use PayPalBR\PayPalPlus\Api\EventsInterface;
use PayPalBR\PayPalPlus\Api\WebHookManagementInterface;

class WebHookManagement implements WebHookManagementInterface
{
    protected $eventWebhook;

    public function __construct(
        EventsInterface $eventWebhook
    ) {
        $this->setEventWebhook($eventWebhook);
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
            $this->logger($array);
            $this->getEventWebhook()->processWebhookRequest($webhookApi);
            $return = [
                [
                    'status' => 200,
                    'message' => $e->getMessage()
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
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/paypalplus-webhook.log');
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
}
<?php
namespace PayPalBR\PayPalPlus\Model;

use oauth;

class WebHookManagement
{

    /**
    * {@inheritdoc}
    */
    public function postWebHook($id, $create_time, $resource_type, $event_type, $summary, $resource, $links, $event_version)
    {

        if (! $id) {
            return false;
        }

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

        return $array;
    }

    protected function logger($array)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/paypalplus.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($array);
    }
}
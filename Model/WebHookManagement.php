<?php
namespace PayPalBR\PayPalPlus\Model;

use oauth;
use \PayPal\Api\VerifyWebhookSignature;
use \PayPal\Api\WebhookEvent;


class WebHookManagement
{

    /**
     * {@inheritdoc}
     */
    public function postWebHook($params)
    {
        $webhook = new \PayPal\Api\Webhook();
        $webhook->setUrl("https://requestb.in/10ujt3c1?uniqid=" . uniqid());


        $webhookEventTypes = array();
        $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
            '{
                "name":"PAYMENT.AUTHORIZATION.CREATED"
            }'
        );
        $webhookEventTypes[] = new \PayPal\Api\WebhookEventType(
            '{
                "name":"PAYMENT.AUTHORIZATION.VOIDED"
            }'
        );
        $webhook->setEventTypes($webhookEventTypes);


         ResultPrinter::printResult("Created Webhook", "Webhook", $output->getId(), $request, $output);

        return $output;

    }



}

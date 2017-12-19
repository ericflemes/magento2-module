<?php
namespace PayPalBR\PayPalPlus\Api;

/**
 * PayPalBR PayPalPlus Event Handler
 *
 * @category   PayPalBR
 * @package    PayPalBR_PayPalPlus
 * @author Dev
 */

class EventsInterface
{
    /**
     * Process the given $webhookEvent
     *
     * @param \PayPal\Api\WebhookEvent $webhookEvent
     */
    public function processWebhookRequest(\PayPal\Api\WebhookEvent $webhookEvent);

    /**
     * Get supported webhook events
     *
     * @return array
     */
    public function getSupportedWebhookEvents();

}
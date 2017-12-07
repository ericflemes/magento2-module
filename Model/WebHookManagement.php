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


        try {
            $output = $webhook->create($apiContext);
        } catch (Exception $ex) {
            // Ignore workflow code segment

                ResultPrinter::printError("Created Webhook Failed. Checking if it is Webhook Number Limit Exceeded. Trying to delete all existing webhooks", "Webhook", "Please Use <a style='color: red;' href='DeleteAllWebhooks.php' >Delete All Webhooks</a> Sample to delete all existing webhooks in sample", $request, $ex);
                if (strpos($data, 'WEBHOOK_NUMBER_LIMIT_EXCEEDED') !== false) {
                    require 'DeleteAllWebhooks.php';
                    try {
                        $output = $webhook->create($apiContext);
                    } catch (Exception $ex) {

                        ResultPrinter::printError("Created Webhook", "Webhook", null, $request, $ex);
                        exit(1);
                    }
                } else {

                    ResultPrinter::printError("Created Webhook", "Webhook", null, $request, $ex);
                    exit(1);
                }
            } else {

                ResultPrinter::printError("Created Webhook", "Webhook", null, $request, $ex);
                exit(1);
            }

        }

         ResultPrinter::printResult("Created Webhook", "Webhook", $output->getId(), $request, $output);

        return $output;

    }

    /**
     * {@inheritdoc}
     */
    public function getWebHook($id)
    {
        /** @var \PayPal\Api\Webhook $webhook */

        $webhookId = $webhook->getId();

        try {
            $output = \PayPal\Api\Webhook::get($webhookId, $apiContext);
        } catch (Exception $ex) {
        NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
            ResultPrinter::printError("Get a Webhook", "Webhook", null, $webhookId, $ex);
            exit(1);
        }


    }

    /**
     * {@inheritdoc}
     */
    public function patchWebHook($params)
    {
        $patch = new \PayPal\Api\Patch();
        $patch->setOp("replace")
            ->setPath("/url")
            ->setValue("https://requestb.in/10ujt3c1?uniqid=". uniqid());

        $patch2 = new \PayPal\Api\Patch();
        $patch2->setOp("replace")
            ->setPath("/event_types")
            ->setValue(json_decode('[{"name":"PAYMENT.SALE.REFUNDED"}]'));

        $patchRequest = new \PayPal\Api\PatchRequest();
        $patchRequest->addPatch($patch)->addPatch($patch2);

        try {
            $output = $webhook->update($patchRequest, $apiContext);
        } catch (Exception $ex) {

            ResultPrinter::printError("Updated a Webhook", "Webhook", null, $patchRequest, $ex);
            exit(1);
        }

         ResultPrinter::printResult("Updated a Webhook", "Webhook", $output->getId(), $patchRequest, $output);

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteWebHook($id)
    {

        try {
            $output = $webhook->delete($apiContext);
        } catch (Exception $ex) {

            ResultPrinter::printError("Delete a Webhook", "Webhook", null, $webhookId, $ex);
            exit(1);
        }

         ResultPrinter::printResult("Delete a Webhook", "Webhook", $webhook->getId(), null, null);

        return $output;


    }

    /**
     * {@inheritdoc}
     */
    public function getEvent($id)
    {
        $params = array(
        'start_time'=>'2014-12-06T11:00:00Z', 'end_time'=>'2014-12-12T11:00:00Z'
        );

        try {
            $output = \PayPal\Api\WebhookEvent::all($params, $apiContext);
        } catch (Exception $ex) {
            ResultPrinter::printError("Search Webhook events", "WebhookEventList", null, null, $ex);
            exit(1);
        }
         ResultPrinter::printResult("Search Webhook events", "WebhookEventList", null, $params, $output);


        return $output;
    }


    /**
     * {@inheritdoc}
     */
    public function verifySignature($params)
    {
        /**
         * This is one way to receive the entire body that you received from PayPal webhook. This is one of the way to retrieve that information.
         * Just uncomment the below line to read the data from actual request.
         */
        /** @var String $requestBody */
        $requestBody = '{"id":"WH-9UG43882HX7271132-6E0871324L7949614","event_version":"1.0","create_time":"2016-09-21T22:00:45Z","resource_type":"sale","event_type":"PAYMENT.SALE.COMPLETED","summary":"Payment completed for $ 21.0 USD","resource":{"id":"80F85758S3080410K","state":"completed","amount":{"total":"21.00","currency":"USD","details":{"subtotal":"17.50","tax":"1.30","shipping":"2.20"}},"payment_mode":"INSTANT_TRANSFER","protection_eligibility":"ELIGIBLE","protection_eligibility_type":"ITEM_NOT_RECEIVED_ELIGIBLE,UNAUTHORIZED_PAYMENT_ELIGIBLE","transaction_fee":{"value":"0.91","currency":"USD"},"invoice_number":"57e3028db8d1b","custom":"","parent_payment":"PAY-7F371669SL612941HK7RQFDQ","create_time":"2016-09-21T21:59:02Z","update_time":"2016-09-21T22:00:06Z","links":[{"href":"https://api.sandbox.paypal.com/v1/payments/sale/80F85758S3080410K","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/payments/sale/80F85758S3080410K/refund","rel":"refund","method":"POST"},{"href":"https://api.sandbox.paypal.com/v1/payments/payment/PAY-7F371669SL612941HK7RQFDQ","rel":"parent_payment","method":"GET"}]},"links":[{"href":"https://api.sandbox.paypal.com/v1/notifications/webhooks-events/WH-9UG43882HX7271132-6E0871324L7949614","rel":"self","method":"GET"},{"href":"https://api.sandbox.paypal.com/v1/notifications/webhooks-events/WH-9UG43882HX7271132-6E0871324L7949614/resend","rel":"resend","method":"POST"}]}';
        /**
        * Receive the entire body that you received from PayPal webhook.
        * Just uncomment the below line to read the data from actual request.
        */
        /** @var String $bodyReceived */
        $bodyReceived = file_get_contents('php://input');

         $headers = array (
          'Client-Pid' => '14910',
          'Cal-Poolstack' => 'amqunphttpdeliveryd:UNPHTTPDELIVERY*CalThreadId=0*TopLevelTxnStartTime=1579e71daf8*Host=slcsbamqunphttpdeliveryd3001',
          'Correlation-Id' => '958be65120106',
          'Host' => 'shiparound-dev.de',
          'User-Agent' => 'PayPal/AUHD-208.0-25552773',
          'Paypal-Auth-Algo' => 'SHA256withRSA',
          'Paypal-Cert-Url' => 'https://api.sandbox.paypal.com/v1/notifications/certs/CERT-360caa42-fca2a594-a5cafa77',
          'Paypal-Auth-Version' => 'v2',
          'Paypal-Transmission-Sig' => 'eDOnWUj9FXOnr2naQnrdL7bhgejVSTwRbwbJ0kuk5wAtm2ZYkr7w5BSUDO7e5ZOsqLwN3sPn3RV85Jd9pjHuTlpuXDLYk+l5qiViPbaaC0tLV+8C/zbDjg2WCfvtf2NmFT8CHgPPQAByUqiiTY+RJZPPQC5np7j7WuxcegsJLeWStRAofsDLiSKrzYV3CKZYtNoNnRvYmSFMkYp/5vk4xGcQLeYNV1CC2PyqraZj8HGG6Y+KV4trhreV9VZDn+rPtLDZTbzUohie1LpEy31k2dg+1szpWaGYOz+MRb40U04oD7fD69vghCrDTYs5AsuFM2+WZtsMDmYGI0pxLjn2yw==',
          'Paypal-Transmission-Time' => '2016-09-21T22:00:46Z',
          'Paypal-Transmission-Id' => 'd938e770-8046-11e6-8103-6b62a8a99ac4',
          'Accept' => '*/*',
        );


        $headers = array_change_key_case($headers, CASE_UPPER);

        $signatureVerification = new VerifyWebhookSignature();
        $signatureVerification->setAuthAlgo($headers['PAYPAL-AUTH-ALGO']);
        $signatureVerification->setTransmissionId($headers['PAYPAL-TRANSMISSION-ID']);
        $signatureVerification->setCertUrl($headers['PAYPAL-CERT-URL']);
        $signatureVerification->setWebhookId("9XL90610J3647323C"); // Note that the Webhook ID must be a currently valid Webhook that you created with your client ID/secret.
        $signatureVerification->setTransmissionSig($headers['PAYPAL-TRANSMISSION-SIG']);
        $signatureVerification->setTransmissionTime($headers['PAYPAL-TRANSMISSION-TIME']);

        $signatureVerification->setRequestBody($requestBody);
        $request = clone $signatureVerification;

        try {
            /** @var \PayPal\Api\VerifyWebhookSignatureResponse $output */
            $output = $signatureVerification->post($apiContext);
        } catch (Exception $ex) {


            ResultPrinter::printError("Validate Received Webhook Event", "WebhookEvent", null, $request->toJSON(), $ex);
            exit(1);
        }

        ResultPrinter::printResult("Validate Received Webhook Event", "WebhookEvent", $output->getVerificationStatus(), $request->toJSON(), $output);

    }

    /**
     * {@inheritdoc}
     */
    public function resendEvent($id)
    {
        return 'Hello API! POST return the $param ' . $param;
    }


   /**
     * {@inheritdoc}
     */
    public function getEventByParams($params)
    {
       try {
            $output = \PayPal\Api\WebhookEventType::availableEventTypes($apiContext);
        } catch (Exception $ex) {

            ResultPrinter::printError("Get List of All Webhook Event Types", "WebhookEventTypeList", null, null, $ex);
            exit(1);
        }

         ResultPrinter::printResult("Get List of All Webhook Event Types", "WebhookEventTypeList", null, null, $output);

        return $output;
    }

}

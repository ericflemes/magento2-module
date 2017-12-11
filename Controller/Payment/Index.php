<?php
/**
 * @author Diego Lisboa <diego@webjump.com.br>
 * @category PayPalBR
 * @package paypalbr\PayPalPlus\
 * @copyright   WebJump (http://www.webjump.com.br)
 *
 * © 2016 WEB JUMP SOLUTIONS
 */

namespace PayPalBR\PayPalPlus\Controller\Payment;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Contains paypal plus api
     *
     * @var \PayPalBR\PayPalPlus\Model\PaypalPlusApi
     */
    protected $paypalPlusApi;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \PayPalBR\PayPalPlus\Model\PaypalPlusApi $paypalPlusApi
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \PayPalBR\PayPalPlus\Model\PaypalPlusApi $paypalPlusApi,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        $this->paypalPlusApi = $paypalPlusApi;
        $this->jsonFactory = $jsonFactory;

        parent::__construct($context);
    }

    /**
     * This is the ajax call that gets called when user arrives to the checkout
     *
     * Get and validate Payment data and send it to PayPal API.
     * If we get no errors, return data to window.checkoutconfig so
     * it can be accessed by the method-renderer.js
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();

        /**
         * Returns an array with the following structure
         *
         * [
         *     'status' => 'success',
         *     'message' => ...
         * ]
         */
        $response = $this->paypalPlusApi->execute();
        if ($response['status'] == 'success') {
            $resultJson
                ->setHttpResponseCode(200)
                ->setData($response['message']);
        } else {
            $resultJson
                ->setHttpResponseCode(400)
                ->setData([
                    'message' => $response['message']
                ]);
        }
        return $resultJson;
    }
}
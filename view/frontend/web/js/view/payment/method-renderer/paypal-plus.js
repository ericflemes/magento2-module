define([
    'Magento_Checkout/js/view/payment/default',
    'jquery',
    'ko',
    'mage/url',
    'mage/storage',
    'Magento_Checkout/js/model/full-screen-loader',
], function (Component, $, ko, urlBuilder, storage,fullScreenLoader) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'PayPalBR_PayPalPlus/payment/paypal-plus'
        },

        initializeIframe: function () {
            var self = this;
            var serviceUrl = urlBuilder.build('paypalplus/payment/index');
            return storage.post(serviceUrl, '')
                .done(function (response) {
                    var approvalUrl = '';
                    for(var i = 0; i<response.links.length; i++) {
                        if (response.links[i].rel == 'approval_url') {
                            approvalUrl = response.links[i].href;
                        }
                    }
                    console.log("Approval URL: " + approvalUrl);
                    var customerData = window.checkoutConfig.customerData;
                    this.paypalObject = PAYPAL.apps.PPP(
                    {
                        "approvalUrl": approvalUrl,
                        "placeholder": "ppplus",
                        "mode": "sandbox" ,
                        "payerFirstName": customerData.firstname,
                        "payerLastName": customerData.lastname,
                        "payerPhone": "05511998548609",
                        "payerEmail": customerData.email,
                        "payerTaxId": customerData.taxvat,
                        "payerTaxIdType": "BR_CPF",
                        "language": "pt_BR",
                        "country": "BR",
                        "enableContinue": "orderPP",
                        "disableContinue": "orderPPs",
                        "iframeHeight": "500",
                        /**
                         * Do stuff after iframe is loaded
                         * @returns {undefined}
                         */
                        onLoad: function () {
                            console.log("Iframe successfully lo aded !");
                        },
                        /**
                         * Continue after payment is verifies (continueButton)
                         *
                         * @param {string} rememberedCards
                         * @param {string} payerId
                         * @param {string} token
                         * @param {string} term
                         * @returns {}
                         */
                        onContinue: function () {
                            $('#continueButton').hide();
                            $('#payNowButton').show();

                            var message = {
                                message: $.mage.__('Payment has been authorized.')
                            };
                            self.messageContainer.addSuccessMessage(message);
                            $('#ppplus').hide();

                            //end aproved card and payment method, run placePendingOrder
                            //self.placePendingOrder();
                        },
                        /**
                         * Handle iframe error (if payment fails for example)
                         *
                         * @param {type} err
                         * @returns {undefined}
                         */
                        onError: function (err) {
                            var message = {
                                message: JSON.stringify(err.cause)
                            };
                            //Display response error
                            //that.messageContainer.addErrorMessage(message);
                        }
                    });
                    // console.log(response);
                }).fail(function (response) {
                    console.log(response);
                });
        },

        doContinue: function () {
            var self = this;

            self.paypalObject.doContinue();

        },

        initialize: function () {

            fullScreenLoader.startLoader();

            this._super();
            this._render();
            var self = this;
            var iframeLoaded = setInterval(function ()
            {
                if ($('#ppplus').length )
                {
                    /*
                    if (!window.checkoutConfig.payment.paypalPlusIframe.api.isQuoteReady || window.checkoutConfig.payment.paypalPlusIframe.api.error) {
                        $('#iframe-warning').hide();
                        $('#iframe-error').show();
                        $('#continueButton').prop("disabled", true);
                        return false;
                    }
                    */
                    self.initializeIframe();
                    fullScreenLoader.stopLoader();
                    clearInterval(iframeLoaded);
                }
            }, 300);
        },

        _render:function(){

        }
    });
});
/**

 * @author Dev <Dev@webjump.com.br>
 * @category PayPalBR
 * @package paypalbr\PayPalPlus\
 * @copyright   WebJump (http://www.webjump.com.br)
 *
 * © 2016 WEB JUMP SOLUTIONS
 *
 */
define([
    'Magento_Checkout/js/view/payment/default',
    'jquery',
    'ko',
    'mage/url',
    'mage/storage',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/postcode-validator'
], function (Component, $, ko, urlBuilder, storage,fullScreenLoader , errorProcesor , quote,  postcodeValidator) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'PayPalBR_PayPalPlus/payment/paypal-plus'
        },
        breakError: false,
        errorProcessor: errorProcesor,
        customerInfo: quote.billingAddress._latestValue,
        paymentApiServiceUrl: 'paypalplus/payment',
        isPaymentReady: false,

        getNamePay: function(){
            return "Pay Pal Plus " + window.checkoutConfig.payment.paypalbr_paypalplus.exibitionName;
        },

        initialize: function () {

            fullScreenLoader.startLoader();
            this.initPayment();
            this._super();
            this._render();
            var self = this;
            var iframeLoaded = setInterval(function () {
                if ($('#ppplus').length) {

                    if (this.breakError) {
                        $('#iframe-warning').hide();
                        $('#iframe-error').show();
                        $('#continueButton').prop("disabled", true);
                        return false;
                    }

                    self.initializeIframe();
                    fullScreenLoader.stopLoader();
                    clearInterval(iframeLoaded);
                }
            }, 300);
        },

        initializeIframe: function () {
            var self = this;
            var serviceUrl = urlBuilder.build('paypalplus/payment/index');
            return storage.post(serviceUrl, '')
                .done(function (response) {
                    var approvalUrl = '';
                    console.log(response);
                    for (var i = 0; i < response.links.length; i++) {
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
                            "mode": "sandbox",
                            "payerFirstName": customerData.firstname,
                            "payerLastName": customerData.lastname,
                            "payerPhone": "05511998548609",
                            "payerEmail": customerData.email,
                            "payerTaxId": customerData.taxvat,
                            "payerTaxIdType": "BR_CPF",
                            "language": "pt_BR",
                            "country": "BR",
                            enableContinue: "orderPP",
                            disableContinue: "orderPPs",
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
                             * @param {string} payerId
                             * @param {string} token
                             * @param {string} term
                             * @returns {}
                             */
                            onContinue: function (payerId, token, term) {
                                $('#continueButton').hide();
                                $('#payNowButton').show();
                                self.payerId = payerId;
                                //Show Place Order button

                                var message = {
                                    message: $.mage.__('Payment has been authorized.')
                                };
                                self.messageContainer.addSuccessMessage(message);

                                if (typeof term !== 'undefined') {
                                    self.term = term;
                                }
                                $('#ppplus').hide();
                                self.placePendingOrder();
                            },

                            /**
                             * Handle iframe error
                             *
                             * @param {type} err
                             * @returns {undefined}
                             */
                            onError: function (err) {

                                this.breakError = true;
                                var message = {
                                    message: JSON.stringify(err.cause)
                                };
                                //Display response error
                                that.messageContainer.addErrorMessage(message);
                            }
                        });

                }).fail(function (response) {
                    var iframeErrorElem = '#iframe-error';
                    if (reason) {
                        if (reason === 'payment_not_ready') {
                            iframeErrorElem = '#iframe-error-payment-not-ready';
                        } else {
                            $(iframeErrorElem).html('');
                            $(iframeErrorElem).append('<div><span>' + reason + '</span></div>');
                        }
                    }
                    $(iframeErrorElem).show();
                    $('#iframe-warning').hide();
                    $('#continueButton').prop("disabled", true);
                    fullScreenLoader.stopLoader();
                });
        },


        placePendingOrder: function () {
            var self = this;
            if (this.placeOrder()) {
                // capture all click events
                document.addEventListener('click', iframe.stopEventPropagation, true);
            }
        },



        doContinue: function () {
            var self = this;
            if (this.validateAddress() !== false) {
                self.paypalObject.doContinue();
            } else {
                var message = {
                    message: $.mage.__('Please verify shipping address.')
                };
                self.messageContainer.addErrorMessage(message);
            }
        },

        /**
         * Validate shipping address.
         *
         * @returns {Boolean}
         */
        validateAddress: function () {

            this.customerData = quote.billingAddress._latestValue;

            if (typeof this.customerData.city === 'undefined' || this.customerData.city.length === 0) {
                return false;
            }

            if (typeof this.customerData.countryId === 'undefined' || this.customerData.countryId.length === 0) {
                return false;
            }
            console.log(this.customerData);

            //if (typeof this.customerData.postcode === 'undefined' || this.customerInfo.postcode.length === 0 || !postcodeValidator.validate(this.customerInfo.postcode, "BR")) {
            //    return false;
            //}

            if (typeof this.customerData.street === 'undefined' || this.customerData.street[0].length === 0) {
                return false;
            }
            if (typeof this.customerData.region === 'undefined' || this.customerData.region.length === 0) {
                return false;
            }
            return true;
        },

        _render: function () {

        }
    });
});



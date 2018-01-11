define([
    'Magento_Checkout/js/view/payment/default',
    'Magento_Paypal/js/model/iframe',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/postcode-validator',
    'ko',
    'mage/url'
], function (
    Component,
    iframe,
    $,
    quote,
    storage,
    errorProcesor,
    fullScreenLoader,
    postcodeValidator,
    ko,
    urlBuilder
    ) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'PayPalBR_PayPal/payment/paypal-plus',
            paymentReady: true,
            paypalPayerId: '',
            payerIdCustomer: '',
            token: '',
            term: ''
        },
        breakError: false,
        errorProcessor: errorProcesor,
        customerInfo: quote.billingAddress._latestValue,
        paymentApiServiceUrl: 'paypalplus/payment',
        isPaymentReady: false,


        getNamePay: function(){
            return "Cartão de Crédito " + window.checkoutConfig.payment.paypalbr_paypalplus.exibitionName;
        },

        isActive: function(){
            return window.checkoutConfig.payment.paypalbr_paypalplus.active;
        },

        paypalObject: {},

        initialize: function () {

            fullScreenLoader.startLoader();
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

        runPayPal: function(approvalUrl) {

            var storage;
            var self = this;
            var telephone = '';
            var firstName = '';
            var lastName = '';
            var email = '';
            var taxVat = '';
            var customerData = window.checkoutConfig.customerData;
            var mode = window.checkoutConfig.payment.paypalbr_paypalplus.mode === "1" ? 'sandbox' : 'live';
            
            storage = $.initNamespaceStorage('paypal-data');
            storage = $.localStorage;

            var isEmpty = true;
            for (var i in customerData) {
                if(customerData.hasOwnProperty(i)) {
                    isEmpty = false;
                }
            }
 
            if(isEmpty){
                telephone =  quote.shippingAddress().telephone ? quote.shippingAddress().telephone  : storage.get('telephone');
            }else{
                telephone = quote.shippingAddress().telephone;
            }

            if(isEmpty){
                firstName =  quote.shippingAddress().firstname ? quote.shippingAddress().firstname : storage.get('firstName');
            }else{
                firstName = customerData.firstname;
            }
            
            if(isEmpty){
                lastName =  quote.shippingAddress().lastname ? quote.shippingAddress().lastname : storage.get('lastName');
            }else{
                lastName = customerData.lastname;
            }
            
            if(isEmpty){
                email =  quote.guestEmail ? quote.guestEmail : storage.get('email');
            }else{
                email = customerData.email;
            }

            if(isEmpty){
                taxVat =  quote.shippingAddress().vatId ? quote.shippingAddress().vatId : storage.get('taxVat');
            }else{
                taxVat = customerData.taxvat;
            }
                        

            storage.set('paypal-data',{'firstName': firstName,
                                'lastName': lastName,
                                'email': email,
                                'taxVat':taxVat,
                                'telephone': telephone});


            this.paypalObject = PAYPAL.apps.PPP(
                {
                    "approvalUrl": approvalUrl,
                    "placeholder": "ppplus",
                    "mode": mode,
                    "payerFirstName": firstName,
                    "payerLastName": lastName,
                    "payerPhone": "055"+telephone,
                    "payerEmail": email,
                    "payerTaxId": taxVat,
                    "payerTaxIdType": "BR_CPF",
                    "language": "pt_BR",
                    "country": "BR",
                    "enableContinue": "continueButton",
                    "disableContinue": "continueButton",
                    "iframeHeight": "420",
                    "rememberedCards": window.checkoutConfig.payment.paypalbr_paypalplus.rememberedCard,
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
                     * @param {string} rememberedCardsToken
                     * @param {string} payerId
                     * @param {string} token
                     * @param {string} term
                     * @returns {}
                     */

                    onContinue: function (rememberedCardsToken, payerId, token, term) {
                        $('#continueButton').hide();
                        $('#payNowButton').show();

                        self.payerId = payerId;

                        var message = {
                            message: $.mage.__('Payment has been proccess.')
                        };
                        self.messageContainer.addSuccessMessage(message);

                        if (typeof term !== 'undefined') {
                            self.term = term;
                        }else{
                            term = '1';
                            self.term = term;
                        }

                        $('#paypalbr_paypalplus_rememberedCardsToken').val(rememberedCardsToken);
                        $('#paypalbr_paypalplus_payerId').val(payerId);
                        $('#paypalbr_paypalplus_token').val(token);
                        $('#paypalbr_paypalplus_term').val(term);

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
           
                        var message = JSON.stringify(err.cause);
                        var ppplusError = message.replace(/[\\"]/g, '');
                        if (typeof err.cause !== 'undefined') {
                            switch (ppplusError)
                            {

                            case "INTERNAL_SERVICE_ERROR":
                                alert ("Ocorreu um erro inesperado, por favor tente novamente."); //pt_BR
                                location.reload(); 
                            case "SOCKET_HANG_UP": 
                            case "socket hang up":
                            case "connect ECONNREFUSED": 
                                alert ("Ocorreu um erro inesperado, por favor tente novamente."); //pt_BR
                                location.reload();
                            case "connect ETIMEDOUT": //javascript fallthrough
                                alert ("Ocorreu um erro inesperado, por favor tente novamente."); //pt_BR
                                location.reload();
                            case "UNKNOWN_INTERNAL_ERROR": //javascript fallthrough
                                alert ("Ocorreu um erro inesperado, por favor tente novamente."); //pt_BR
                                location.reload();
                            case "fiWalletLifecycle_unknown_error": //javascript fallthrough
                                alert ("Ocorreu um erro inesperado, por favor tente novamente."); //pt_BR
                                location.reload();
                            case "Failed to decrypt term info": //javascript fallthrough
                                alert ("Ocorreu um erro inesperado, por favor tente novamente."); //pt_BR
                                location.reload();
                            case "RESOURCE_NOT_FOUND": 
                                alert ("Ocorreu um erro inesperado, por favor tente novamente."); //pt_BR
                                location.reload();
                            case "INTERNAL_SERVER_ERROR":
                                alert ("Ocorreu um erro inesperado, por favor tente novamente."); //pt_BR
                                location.reload();
                            break;
                            case "RISK_N_DECLINE": 
                                alert ("Por favor utilize outro cartão, caso o problema persista entre em contato com o PayPal (0800-047-4482)."); 
                                location.reload();
                            case "NO_VALID_FUNDING_SOURCE_OR_RISK_REFUSED": 
                                alert ("Por favor utilize outro cartão, caso o problema persista entre em contato com o PayPal (0800-047-4482)."); 
                                location.reload();
                            case "TRY_ANOTHER_CARD": //javascript fallthrough
                            case "NO_VALID_FUNDING_INSTRUMENT":
                                alert ("Seu pagamento não foi aprovado. Por favor utilize outro cartão, caso o problema persista entre em contato com o PayPal (0800-047-4482)."); 
                                location.reload();
                            break;
                            case "CARD_ATTEMPT_INVALID":
                                alert ("Ocorreu um erro inesperado, por favor tente novamente."); //pt_BR
                                location.reload();
                            break;
                            case "INVALID_OR_EXPIRED_TOKEN":
                                alert ("A sua sessão expirou, por favor tente novamente."); //pt_BR
                                location.reload();
                            break;
                            case "CHECK_ENTRY":
                                alert ("Por favor revise os dados de Cartão de Crédito inseridos."); //pt_BR
                                location.reload();
                            break;
                            default: //unknown error & reload payment flow
                                alert ("Ocorreu um erro inesperado, por favor tente novamente."); //pt_BR
                                location.reload();
                            }
                        }


                    }
                }
            );
        },

        initializeIframe: function () {
            var self = this;
            var serviceUrl = urlBuilder.build('paypalplus/payment/index');
            var approvalUrl = '';
            fullScreenLoader.startLoader();
            storage.post(serviceUrl, '')
            .done(function (response) {
                console.log(response);
                $('#paypalbr_paypalplus_payId').val(response.id);
                for (var i = 0; i < response.links.length; i++) {
                    if (response.links[i].rel == 'approval_url') {
                        approvalUrl = response.links[i].href;
                    }
                }
                //console.log("Approval URL: " + approvalUrl);
                self.runPayPal(approvalUrl);
            })
            .fail(function (response) {
                console.log("ERRO");
                console.log(response);
                var iframeErrorElem = '#iframe-error';

                $(iframeErrorElem).html('');
                $(iframeErrorElem).append($.mage.__('<div><span>Error to load iframe</span></div>'));

                $(iframeErrorElem).show();
                $('#iframe-warning').hide();
                $('#continueButton').prop("disabled", true);
            })
            .always(function () {
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

        getData: function () {
            return {
                'method': this.item.method,
                'additional_data': {
                    'payId': $('#paypalbr_paypalplus_payId').val(),
                    'rememberedCardsToken': $('#paypalbr_paypalplus_rememberedCardsToken').val(),
                    'payerId': $('#paypalbr_paypalplus_payerId').val(),
                    'token': $('#paypalbr_paypalplus_token').val(),
                    'term': $('#paypalbr_paypalplus_term').val(),
                }
            };
        },

        initObservable: function () {
                this._super()
                    .observe([
                        'payId',
                        'rememberedCardsToken',
                        'payerId',
                        'token',
                        'term',
                    ]);

                return this;
            },

        /**
         * Validate shipping address.
         *
         * @returns {Boolean}
         */
        validateAddress: function () {


            this.customerData = quote.billingAddress._latestValue;
            if(!this.customerData.city){
              this.customerData = quote.shippingAddress._latestValue;  
            }
            if (typeof this.customerData.city === 'undefined' || this.customerData.city.length === 0) {
                return false;
            }

            if (typeof this.customerData.countryId === 'undefined' || this.customerData.countryId.length === 0) {
                return false;
            }
   
            if (typeof this.customerData.postcode === 'undefined' || this.customerData.postcode.length === 0) {
                return false;
            }

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



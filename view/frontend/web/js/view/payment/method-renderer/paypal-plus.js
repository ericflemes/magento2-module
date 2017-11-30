define([
    'Magento_Checkout/js/view/payment/default',
    'ko',
    'jquery',
    'mage/url',
    'mage/storage',
], function (Component , ko , $ , urlBuilder , storage) {
    'use strict';

    return Component.extend({


        getLink: function () {

             var self = this;
             var serviceUrl = urlBuilder.build('rest/default/V1/paypalplus/obtainaccesstoken');
             return storage.post(
                 serviceUrl,
                 ''
             ).done(
                 function (response) {

                         var ppp = PAYPAL.apps.PPP({
                             "approvalUrl": response,
                             "placeholder": "ppplusDiv",
                             "mode": "sandbox" ,
                            "payerFirstName": "Diego",
                            "payerLastName": " Lisboa",
                            "payerPhone": "05511998548609",
                            "payerEmail": "diego.giglioli@gmail.com",
                            "payerTaxId": "31767933835",
                            "payerTaxIdType": "BR_CPF",
                            "language": "pt_BR",
                            "country": "BR",
                            "enableContinue": "orderPP",
                            "disableContinue": "orderPPs",
                        });

                    console.log(response);
                 }
             ).fail(
                 function (response) {
                     console.log(response);
                 }
             );
        },

        initialize: function () {

            this._super();
            this._render();


        },
        _render:function(){
          this.getLink();
        },

        defaults: {
            template: 'PayPalBR_PayPalPlus/payment/paypal-plus'
        },



    });
});

/*browser:true*/
/*global define*/
define(
        [
            'uiComponent',
            'Magento_Checkout/js/model/payment/renderer-list'
        ],
        function (
                Component,
                rendererList
                ) {
            'use strict';
            rendererList.push(
                    {
                        type: 'paypalbr_paypalplus',
                        component: 'PayPalBR_PayPalPlus/js/view/payment/method-renderer/paypal-plus'
                    }

            );

            /** Add view logic here if needed */
            return Component.extend({});
        }
);
/**
 * qbo_PayPalPlusMx Magento JS component
 *
 * @category    qbo
 * @package     qbo_PayPalPlusMx
 * @author      Ivan Weiler & Stjepan Udovičić
 * @copyright   qbo (http://www.qbo.tech)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
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
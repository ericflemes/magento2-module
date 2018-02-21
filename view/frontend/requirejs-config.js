var config = {
    map: {
        '*': {
            'Magento_Checkout/js/action/select-shipping-method':'PayPalBR_PayPal/js/action/select-shipping-method',
            'Magento_SalesRule/js/view/payment/discount':'PayPalBR_PayPal/js/view/payment/discount',
            'Magento_Checkout/js/model/step-navigator':'PayPalBR_PayPal/js/model/step-navigator'
        }
    },
    paths:{
        "ppplus":"https://www.paypalobjects.com/webstatic/ppplusdcc/ppplusdcc.min.js",
        "legalize":"http://paypal.github.io/legalize.js/dist/legalize.min.js"
    },
    shim:{
        'ppplus':{
            'deps':[
            	'legalize', 
            	'jquery/jquery.cookie'
            ]
        }
    }
};
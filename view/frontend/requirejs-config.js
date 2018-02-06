var config = {
    map: {
        '*': {
            'Magento_Checkout/js/action/select-shipping-method':'PayPalBR_PayPal/js/action/select-shipping-method'
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
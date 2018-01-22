var config = {
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
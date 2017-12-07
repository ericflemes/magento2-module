# Magento 2 PayPalBR


## How to install

**This module is now available through *Packagist* ! You don't need to specify the repository anymore.**

Add the following lines into your composer.json
 
```
...
"require":{
    ...
    "br-paypaldev/magento2-module":"~0.1.3"
 }
```
or simply digit 
```
composer require br-paypaldev/magento2-module
```
 
Then type the following commands from your Magento root:

```
$ composer update
$ ./bin/magento setup:upgrade
$ ./bin/magento setup:di:compile
```
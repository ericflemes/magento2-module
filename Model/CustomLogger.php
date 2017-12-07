<?php

namespace PayPalBR\PayPalPlus\Model;
use Monolog\Logger;

class CustomLogger extends \Magento\Framework\Logger\Handler\Debug
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/paypalplus.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;
}
<?php
/**
 * Class AuthorizeCommand
 *
 * @author      Webjump Core Team <dev@webjump.com>
 * @copyright   2016 Webjump (http://www.webjump.com.br)
 * @license     http://www.webjump.com.br Copyright
 *
 * @link        http://www.webjump.com.br
 */

namespace PayPalBR\PayPalPlus\Gateway\Transaction\PayPalPlus\Command;

use PayPalBR\PayPalPlus\Gateway\Transaction\Base\Command\AbstractApiCommand;

class AuthorizeCommand extends AbstractApiCommand
{
    /**
     * @param $request
     * @return mixed
     */
    protected function sendRequest($request)
    {
        if (!isset($request)) {
            throw new \InvalidArgumentException('PayPalPlus Request object should be provided');
        }

        return $request;
    }
}

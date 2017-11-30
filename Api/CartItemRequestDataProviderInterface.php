<?php
/**
 * Class CartItemRequestProvider
 *
 * @author      Webjump Core Team <dev@webjump.com>
 * @copyright   2016 Webjump (http://www.webjump.com.br)
 * @license     http://www.webjump.com.br Copyright
 *
 * @link        http://www.webjump.com.br
 */

namespace PayPalBR\PayPalPlus\Api;


interface CartItemRequestDataProviderInterface
{
    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getItemReference();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @return float
     */
    public function getUnitCostInCents();

    /**
     * @return float
     */
    public function getTotalCostInCents();
}

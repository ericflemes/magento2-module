<?php
/**
 * Class Validator
 *
 * @author      Webjump Core Team <dev@webjump.com>
 * @copyright   2016 Webjump (http://www.webjump.com.br)
 * @license     http://www.webjump.com.br Copyright
 *
 * @link        http://www.webjump.com.br
 */

namespace PayPalBR\PayPalPlus\Gateway\Transaction\PayPalPlus\Resource\Create\Response;

use Magento\Payment\Gateway\Validator\ValidatorInterface;
use PayPalBR\PayPalPlus\Gateway\Transaction\Base\ResourceGateway\Response\AbstractValidator;

class Validator extends AbstractValidator implements ValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(array $validationSubject)
    {
        if (!isset($validationSubject['response'])) {
            throw new \InvalidArgumentException('MundiPagg Credit Card Authorize Response object should be provided');
        }

        $isValid = true;
        $fails = [];

        return $this->createResult($isValid, $fails);
    }
}

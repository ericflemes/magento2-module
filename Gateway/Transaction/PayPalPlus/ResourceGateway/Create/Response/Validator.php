<?php

namespace PayPalBR\PayPalPlus\Gateway\Transaction\PayPalPlus\ResourceGateway\Create\Response;

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
            throw new \InvalidArgumentException('PayPalPlus Credit Card Authorize Response object should be provided');
        }

        $isValid = true;
        $fails = [];

        return $this->createResult($isValid, $fails);
    }
}

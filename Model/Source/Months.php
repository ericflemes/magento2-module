<?php

namespace PayPalBR\PayPalPlus\Model\Source;

class Months implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function  toOptionArray()
    {
        return [
            ['value' => 3, 'label' => 3],
            ['value' => 6, 'label' => 6],
            ['value' => 9, 'label' => 9],
            ['value' => 12, 'label' => 12],
        ];
    }
}

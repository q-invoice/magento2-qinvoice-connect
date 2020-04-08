<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

class Method implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'excl', 'label' => __('Prices without VAT are leading')],
            ['value' => 'incl', 'label' => __('Prices with VAT included are leading')]
        ];
    }
}

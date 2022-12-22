<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Method implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'automatic', 'label' => __('Automatic, with VAT for consumers, without for companies')],
            ['value' => 'excl', 'label' => __('Prices without VAT are leading')],
            ['value' => 'incl', 'label' => __('Prices with VAT included are leading')],
            ['value' => 'customer_groups', 'label' => __('Define separate rules for customer groups')]
        ];
    }
}

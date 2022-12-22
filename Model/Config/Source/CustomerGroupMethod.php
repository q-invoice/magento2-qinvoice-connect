<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CustomerGroupMethod implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'excl', 'label' => __('Prices without VAT are leading')],
            ['value' => 'incl', 'label' => __('Prices with VAT included are leading')],
        ];
    }
}

<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Relation implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('No, do nothing')],
            ['value' => 1, 'label' => __('Save or update customer')]
        ];
    }
}

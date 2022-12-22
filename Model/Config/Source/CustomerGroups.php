<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CustomerGroups implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'foo', 'label' => __('Foo')],
            ['value' => 'bar', 'label' => __('Bar')],
        ];
    }
}

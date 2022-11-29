<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ApiVersion implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1_4', 'label' => __('v1.4') .' '. __('Recommended')],
            ['value' => '1_3', 'label' => __('v1.3')],
            ['value' => '1_2', 'label' => __('v1.2')],
        ];
    }
}

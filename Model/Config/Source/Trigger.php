<?php

/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

class Trigger implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'order', 'label' => __('On every order')],
            ['value' => 'payment', 'label' => __('Only on successful payment')],
            ['value' => 'complete', 'label' => __('When order is marked complete')]
        ];
    }
}

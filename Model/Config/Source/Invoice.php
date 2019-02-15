<?php

/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

class Invoice implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Save invoice as draft')],
            ['value' => 1, 'label' => __('Finalize invoice (save as PDF)')],
            ['value' => 2, 'label' => __('Finalize and send via email')]
        ];
    }
}
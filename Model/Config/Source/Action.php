<?php

/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

class Action implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Save document as draft')],
            ['value' => 1, 'label' => __('Finalize document (save as PDF)')],
            ['value' => 2, 'label' => __('Finalize and send via email')]
        ];
    }
}
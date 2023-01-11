<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class LayoutSelector implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'fixed', 'label' => __('One layout for all customers')],
            ['value' => 'customer_groups', 'label' => __('Define layouts based on customer groups')]
        ];
    }
}

//Psgqi9USu4UfTDH
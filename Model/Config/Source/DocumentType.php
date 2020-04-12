<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class DocumentType implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'invoice', 'label' => __('Invoice')],
            ['value' => 'proforma', 'label' => __('Proforma invoice')],
            ['value' => 'orderconfirmation', 'label' => __('Orderconfirmation')],
            ['value' => 'quote', 'label' => __('Quote')]
        ];
    }
}

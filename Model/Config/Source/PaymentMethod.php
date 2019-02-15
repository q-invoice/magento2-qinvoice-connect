<?php

/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

class PaymentMethod implements \Magento\Framework\Option\ArrayInterface
{
    protected $paymentHelper;

    /**
     * @param \Magento\Payment\Helper\Data $paymentHelper
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper
    )
    {
        $this->paymentHelper = $paymentHelper;
    }


    public function toOptionArray()
    {
        $methods = $this->paymentHelper->getPaymentMethodList();
        $methodsArray = [];

        foreach ($methods as $code => $title) {
            $methodsArray[] = ['value' => $code, 'label' => $title];
        }
        return $methodsArray;
    }
}
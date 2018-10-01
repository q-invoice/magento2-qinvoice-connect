<?php

/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderSaveAfter implements ObserverInterface
{
    public function __construct(
        \Qinvoice\Connect\Model\Call $call
    )
    {
        $this->_call = $call;

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $this->_call->orderStatusChange($order);
    }

}

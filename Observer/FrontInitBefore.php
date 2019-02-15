<?php

/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Observer;

use Magento\Framework\Event\ObserverInterface;

class FrontInitBefore implements ObserverInterface
{
    protected $_call;

    public function __construct(
        \Qinvoice\Connect\Model\Call $call
    )
    {
        $this->_call = $call;

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_call->qinvoiceCall();
    }

}

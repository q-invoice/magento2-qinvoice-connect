<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Qinvoice\Connect\Model\Call;

class FrontInitBefore implements ObserverInterface
{
    protected $_call;

    public function __construct(
        Call $call
    ) {
        $this->_call = $call;
    }

    public function execute(Observer $observer)
    {
        $this->_call->qinvoiceCall();
    }
}

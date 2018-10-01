<?php

/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Controller\Index;
class Index extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Qinvoice\Connect\Model\Connect $con,
        \Magento\Sales\Api\Data\OrderInterface $order
    )
    {
        $this->connect = $con;
        $this->order = $order;
        parent::__construct($context);
    }

    public function execute()
    {
        $order = $this->order->loadByIncrementId('000000001');
        $connect = $this->connect->sendOnOrder($order);
    }
}

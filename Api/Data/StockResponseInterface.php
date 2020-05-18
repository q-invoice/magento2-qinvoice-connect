<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Api\Data;

interface StockResponseInterface extends \Qinvoice\Connect\Api\Data\BaseResponseInterface
{
    /**
     * @return string[]
     */
    public function getData();
}

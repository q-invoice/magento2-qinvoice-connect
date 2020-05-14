<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Api\Data;

interface StoresResponseInterface extends \Qinvoice\Connect\Api\Data\BaseResponseInterface
{
    /**
     * @return \Magento\Store\Api\Data\StoreInterface[]|Null
     */
    public function getData();
}

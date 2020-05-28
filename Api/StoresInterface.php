<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Api;

interface StoresInterface
{
    const RESPONSE_CODE = 920;
    const RESPONSE_MESSAGE = '%d stores found';

    /**
     * @return \Qinvoice\Connect\Api\Data\StoresResponseInterface
     */
    public function get();
}

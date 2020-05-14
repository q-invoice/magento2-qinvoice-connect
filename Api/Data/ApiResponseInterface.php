<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Api\Data;

interface ApiResponseInterface
{
    /**
     * @return \Qinvoice\Connect\Api\Data\ResponseDataInterface
     */
    public function getResponse();

    /**
     * @return array
     */
    public function getData();
}

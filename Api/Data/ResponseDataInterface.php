<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Api\Data;

interface ResponseDataInterface
{
    /**
     * @return string
     */
    public function getVersion();

    /**
     * @return int
     */
    public function getCode();

    /**
     * @return string
     */
    public function getMessage();
}

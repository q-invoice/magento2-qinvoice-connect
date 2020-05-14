<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Api\Data;

interface InstallationValidatorResponseInterface extends \Qinvoice\Connect\Api\Data\BaseResponseInterface
{
    /**
     * @return String|Null
     */
    public function getData();
}

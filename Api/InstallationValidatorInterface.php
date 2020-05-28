<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Api;

interface InstallationValidatorInterface
{
    const RESPONSE_CODE = 999;
    const RESPONSE_MESSAGE = "Plugin installed.";

    /**
     * @return \Qinvoice\Connect\Api\Data\InstallationValidatorResponseInterface
     */
    public function vlidate();
}

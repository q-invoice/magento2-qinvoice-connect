<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Api\Data;

interface ExportResponseInterface extends \Qinvoice\Connect\Api\Data\BaseResponseInterface
{
    /**
     * @return array
     */
    public function getData();
}

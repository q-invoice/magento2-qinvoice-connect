<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Api;

interface ExportInterface
{
    const SUCESS_CODE = 910;
    const STORE_NOT_FOUND_CODE = 130;
    const SUCESS_MESSAGE = "%s items exported";
    const STORE_NOT_FOUND_MESSAGE = "Could not read from store ID %d";

    /**
     * @param string $store_id
     * @return \Qinvoice\Connect\Api\Data\ExportResponseInterface
     */
    public function export($storeId = "default");
}

<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Api;

interface StockInterface
{
    const RESPONSE_CODE = 900;
    const ITEM_NOT_FOUND_RESPONSE_CODE = 102;
    const CANNOT_UPDATE_STOCK_REPONSE_CODE = 199;
    const RESPONSE_MESSAGE = 'Product updated successfully';

    /**
     * @param string $sku
     * @param int $quantity
     * @return \Qinvoice\Connect\Api\Data\StoresResponseInterface
     */
    public function update($sku, $quantity);
}

<?php


namespace Qinvoice\Connect\Api\Data;


interface StockUpdateRequestInterface
{
    /**
     * @return string
     */
    public function getSku();

    /**
     * @return int
     */
    public function getQuantity();
}

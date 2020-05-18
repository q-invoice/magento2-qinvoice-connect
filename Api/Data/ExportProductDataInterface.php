<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Api\Data;

interface ExportProductDataInterface
{
    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return string
     */
    public function getSku();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPrice();

    /**
     * @return string
     */
    public function getWeight();

    /**
     * @return string
     */
    public function getThumbnail();

    /**
     * @return string
     */
    public function getSpecialPrice();

    /**
     * @return string
     */
    public function getStock();

    /**
     * @return string
     */
    public function getMinStock();

    /**
     * @return string
     */
    public function getVat();

    /**
     * @return string[]
     */
    public function getTierPrices();
}

<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */
namespace Qinvoice\Connect\Model\Data;

use Qinvoice\Connect\Api\Data\ExportProductDataInterface;

class ExportProductData implements ExportProductDataInterface
{
    private $entityId;
    private $sku;
    private $name;
    private $price;
    private $weight;
    private $thumbnail;
    private $specialPrice;
    private $stock;
    private $minStock;
    private $vat;
    private $tierPrices;

    /**
     * @inheritDoc
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @param mixed $entityId
     * @return ExportProductData
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param mixed $sku
     * @return ExportProductData
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return ExportProductData
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     * @return ExportProductData
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param mixed $weight
     * @return ExportProductData
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param mixed $thumbnail
     * @return ExportProductData
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSpecialPrice()
    {
        return $this->specialPrice;
    }

    /**
     * @param mixed $specialPrice
     * @return ExportProductData
     */
    public function setSpecialPrice($specialPrice)
    {
        $this->specialPrice = $specialPrice;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param mixed $stock
     * @return ExportProductData
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMinStock()
    {
        return $this->minStock;
    }

    /**
     * @param mixed $minStock
     * @return ExportProductData
     */
    public function setMinStock($minStock)
    {
        $this->minStock = $minStock;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param mixed $vat
     * @return ExportProductData
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTierPrices()
    {
        return $this->tierPrices;
    }

    /**
     * @param array $tierPrices
     * @return ExportProductData
     */
    public function setTierPrices($tierPrices)
    {
        $this->tierPrices = $tierPrices;
        return $this;
    }
}

<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model;

use Magento\Framework\DataObject;

/**
 * Class Document
 * @package Qinvoice\Connect\Model
 */
class Document
{
    const ROOT_NAME = "request";

    /** @var array  */
    private $items = [];

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function addItem($key, $value)
    {
        $this->items[$key] = $value;
        return $this;
    }
}

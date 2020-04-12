<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

use Magento\Catalog\Model\Product;
use Magento\Framework\Option\ArrayInterface;

class Attribute implements ArrayInterface
{
    public function __construct(
        Product $product
    ) {
        $this->product = $product;
    }

    public function toOptionArray()
    {
        $attributes = $this->product->getAttributes();
        $attributeArray = [];

        foreach ($attributes as $attribute) {
            $attributeArray[] = ['value' => $attribute->getFrontendLabel(), 'label' => $attribute->getAttributeCode()];
        }
        return $attributeArray;
    }
}

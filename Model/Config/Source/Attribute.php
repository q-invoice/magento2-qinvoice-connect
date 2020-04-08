<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Model\Config\Source;

class Attribute implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Magento\Catalog\Model\Product $product
    )
    {
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

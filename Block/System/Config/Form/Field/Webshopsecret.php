<?php
namespace Qinvoice\Connect\Block\System\Config\Form\Field;

class Webshopsecret extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setReadonly(1);
        return parent::render($element);
    }
}

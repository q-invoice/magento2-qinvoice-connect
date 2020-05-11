<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Block\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Webshopsecret extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->setReadonly(1);
        return parent::render($element);
    }

    public function getCopyScript(AbstractElement $element)
    {
        return '<script>function copyKey() {
                  var copyText = document.getElementById("' . $element->getHtmlId() . '");
                  copyText.select();
                  copyText.setSelectionRange(0, 99999);
                  document.execCommand("copy");
            }</script>';
    }

    /**
     * Render element value
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($element->getTooltip()) {
            $html = '<td class="value with-tooltip">';
            $html .= $this->_getElementHtml($element);
            $html .= '<div class="tooltip"><span class="help"><span></span></span>';
            $html .= '<div class="tooltip-content">' . $element->getTooltip() . '</div></div>';
        } else {
            $html = '<td class="value">';
            $html .= $this->_getElementHtml($element);
        }
        $html .= '<a onclick="copyKey()" href="#' . $element->getHtmlId() . '">Copy to Clipboard</a>';
        $html .= $this->getCopyScript($element);
        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }
        $html .= '</td>';
        return $html;
    }
}

<?php

namespace Qinvoice\Connect\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Vendor\Module\Block\Adminhtml\Form\Field\CustomColumn;

class DynamicField extends AbstractFieldArray
{
    private $dropdownRenderer;

    protected function _prepareToRender()
    {
        $this->addColumn(
            'attribute_name',
            [
                'label' => __('Customer Group'),
                'renderer' => $this->getDropdownRenderer(),
            ]
        );
        $this->addColumn(
            'dropdown_field',
            [
                'label' => __('Purchaseover'),
                'class' => 'required-entry',
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];
        $dropdownField = $row->getDropdownField();
        if ($dropdownField !== null) {
            $options['option_' . $this->getDropdownRenderer()->calcOptionHash($dropdownField)] = 'selected="selected"';
        }
        $row->setData('attributes', $options);
    }

    private function getDropdownRenderer()
    {
        if (!$this->dropdownRenderer) {
            $this->dropdownRenderer = $this->getLayout()->createBlock(
                CustomColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]);
        }
        return $this->dropdownRenderer;
    }
}
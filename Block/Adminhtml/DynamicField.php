<?php

namespace Qinvoice\Connect\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Qinvoice\Connect\Block\Adminhtml\Form\Field\CustomerGroupColumn;
use Qinvoice\Connect\Block\Adminhtml\Form\Field\MethodColumn;

class DynamicField extends AbstractFieldArray
{
    private $dropdownRenderer;

    protected function _prepareToRender()
    {
        $this->addColumn(
            'attribute_name',
            [
                'label' => __('Customer Group'),
                'renderer' => $this->getCustomerGroupDropdownRenderer(),
            ]
        );
        $this->addColumn(
            'dropdown_field',
            [
                'label' => __('Method'),
                'renderer' => $this->getMethodDropdownRenderer(),
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
            $options['option_' . $this->getCustomerGroupDropdownRenderer()->calcOptionHash($dropdownField)] = 'selected="selected"';
        }
        $row->setData('attributes', $options);
    }

    private function getCustomerGroupDropdownRenderer()
    {
        if (!$this->dropdownRenderer) {
            $this->dropdownRenderer = $this->getLayout()->createBlock(
                CustomerGroupColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]);
        }
        return $this->dropdownRenderer;
    }

    private function getMethodDropdownRenderer()
    {
        if (!$this->dropdownRenderer) {
            $this->dropdownRenderer = $this->getLayout()->createBlock(
                MethodColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]);
        }
        return $this->dropdownRenderer;
    }
}
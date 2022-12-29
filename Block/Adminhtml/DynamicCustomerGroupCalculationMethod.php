<?php

namespace Qinvoice\Connect\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Qinvoice\Connect\Block\Adminhtml\Form\Field\CustomerGroupColumn;
use Qinvoice\Connect\Block\Adminhtml\Form\Field\MethodColumn;

class DynamicCustomerGroupCalculationMethod extends AbstractFieldArray
{
    private $groupDropdownRenderer;
    private $methodDropdownRenderer;

    protected function _prepareToRender()
    {
        $this->addColumn(
            'customer_group_id',
            [
                'label' => __('Customer Group'),
                'renderer' => $this->getCustomerGroupDropdownRenderer(),
            ]
        );
        $this->addColumn(
            'calculation_method',
            [
                'label' => __('MethodSelector'),
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
        $row->setData('option_extra_attrs', $options);
    }

    private function getCustomerGroupDropdownRenderer()
    {
        if (!$this->groupDropdownRenderer) {
            $this->groupDropdownRenderer = $this->getLayout()->createBlock(
                CustomerGroupColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]);
        }
        return $this->groupDropdownRenderer;
    }

    private function getMethodDropdownRenderer()
    {
        if (!$this->methodDropdownRenderer) {
            $this->methodDropdownRenderer = $this->getLayout()->createBlock(
                MethodColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]);
        }
        return $this->methodDropdownRenderer;
    }
}
<?php

namespace Qinvoice\Connect\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Qinvoice\Connect\Block\Adminhtml\Form\Field\CustomerGroupColumn;
use Qinvoice\Connect\Block\Adminhtml\Form\Field\MethodColumn;

class DynamicCustomerGroupLayoutCode extends AbstractFieldArray
{
    private $groupDropdownRenderer;

    protected function _prepareToRender()
    {
        $this->addColumn(
            'customer_group',
            [
                'label' => __('Customer Group'),
                'renderer' => $this->getCustomerGroupDropdownRenderer(),
            ]
        );
        $this->addColumn(
            'layout_code',
            [
                'label' => __('Layout code'),
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

}
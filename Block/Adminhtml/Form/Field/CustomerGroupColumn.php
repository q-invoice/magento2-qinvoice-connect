<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Block\Adminhtml\Form\Field;

use Magento\Customer\Model\Customer\Source\GroupSourceInterface;
use Magento\Framework\View\Element\Html\Select;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ObjectManager;

class CustomerGroupColumn extends Select
{
    protected $groupdata;

    public function __construct(Context $context, GroupSourceInterface $groupdata = null, array $data = [])
    {
        $this->groupdata = $groupdata
            ?: ObjectManager::getInstance()->get(GroupSourceInterface::class);
        parent::__construct($context, $data);
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    public function setInputId($value)
    {
        return $this->setId($value);
    }

    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    private function getSourceOptions()
    {
        $customerGroups = $this->groupdata->toOptionArray();
        return $customerGroups;
    }
}
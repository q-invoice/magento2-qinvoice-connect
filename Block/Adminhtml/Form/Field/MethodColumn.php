<?php

namespace Qinvoice\Connect\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\Customer\Attribute\Source\GroupSourceLoggedInOnlyInterface;
use Magento\Framework\App\ObjectManager;

class MethodColumn extends Select
{
    protected $methoddata;

    public function __construct(Context $context, array $data = [])
    {
        $this->methoddata = \Qinvoice\Connect\Model\Config\Source\CustomerGroupMethod::class;
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
<?php

namespace Qinvoice\Connect\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\Customer\Attribute\Source\GroupSourceLoggedInOnlyInterface;
use Magento\Framework\App\ObjectManager;

class CustomerGroupColumn extends Select
{
    protected $groupdata;

    public function __construct(Context $context, GroupSourceLoggedInOnlyInterface $groupdata = null, array $data = [])
    {
        $this->groupdata = $groupdata
            ?: ObjectManager::getInstance()->get(GroupSourceLoggedInOnlyInterface::class);
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
<?php
namespace Qinvoice\Connect\Block\System\Config\Form\Field;

class Webshopsecret extends \Magento\Config\Block\System\Config\Form\Field
{   
	protected $_storeManager;
	
	public function __construct
	(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\ConfigResource\ConfigInterface  $resourceConfig
	)
	{
		$this->_storeManager = $storeManager;
		$this->resourceConfig = $resourceConfig;
	}
	
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
		 if ($element->getValue() == '') {
        	$value = sha1( $this->_storeManager->getStore()->getName() . $this->_storeManager->getStore()->getBaseUrl() );
            $element->setValues( array('value' => $value ));
            $this->resourceConfig->saveConfig(
				'invoice_options/invoice/webshop_secret',
				$value,
				\Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
				\Magento\Store\Model\Store::DEFAULT_STORE_ID
			);
        }
        $element->setReadonly('readonly');
		
        return parent::_getElementHtml($element);
    }    
}

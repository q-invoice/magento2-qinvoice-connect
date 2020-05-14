<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Block\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\Integration\Model\Oauth\Token\Provider;
use Qinvoice\Connect\Setup\Patch\Data\CreateIntegrationUser;

class Webshopsecret extends Field
{
    /**
     * @var IntegrationServiceInterface
     */
    private $integrationService;
    /**
     * @var Provider
     */
    private $tokenProdvider;

    /**
     * Webshopsecret constructor.
     * @param Context $context
     * @param IntegrationServiceInterface $integrationService
     * @param Provider $tokenProdvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        IntegrationServiceInterface $integrationService,
        Provider $tokenProdvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->integrationService = $integrationService;
        $this->tokenProdvider = $tokenProdvider;
    }

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
        $this->setIntegrationKey($element);
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

    /**
     * @param $element
     */
    private function setIntegrationKey($element)
    {
        $integration = $this->integrationService->findByName(CreateIntegrationUser::Q_INVOICE_INTEGRATION_NAME);
        $consumerId = $integration->getConsumerId();
        /** @var \Magento\Integration\Model\Oauth\Token $token */
        $token = $this->tokenProdvider->getIntegrationTokenByConsumerId($consumerId);
        $element->setValue($token->getToken());
    }
}

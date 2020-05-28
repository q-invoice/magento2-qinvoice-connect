<?php
/**
 * Copyright ©q-invoice B.V.. All rights reserved.
 */

namespace Qinvoice\Connect\Block\System\Config\Form\Field;

use Magento\Backend\Block\AbstractBlock;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Qinvoice\Connect\Service\ModuleVersion;
use Magento\Framework\View\Element\BlockInterface;

class ModuleVersionField extends AbstractBlock implements RendererInterface
{
    /**
     * @var ModuleVersion
     */
    private $moduleVersion;

    /**
     * ModuleVersionField constructor.
     * @param ModuleVersion $moduleVersion
     */
    public function __construct(
        ModuleVersion $moduleVersion
    ) {
        $this->moduleVersion = $moduleVersion;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element = null)
    {
        try {
            $version = $this->moduleVersion->get();
        } catch (\Exception $e) {
            return "";
        }

        $html = '<tr id="row_invoice_options_invoice_api_url">
                <td class="label">
                    <label for="invoice_options_invoice_api_url">
                        <span>' . __('Module Version') . '</span>
                    </label>
                </td>
                <td class="value">
                ' . $version . '
                </td><td class=""></td>
                </tr>';
        return $html;
    }

    public function toHtml()
    {
        return $this->render();
    }
}

<?php

namespace Zero1\LiveChat\Block\Adminhtml\Config;

class Description extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '<img src="https://www.zero1.co.uk/wp-content/uploads/2023/01/cropped-new-zero-1-logo-2.png" width="80" height="38">
        <h2>ZERO-1 Ecommerce</h2>
        <p>Sign up for LiveChat <a href="https://www.livechat.com/"> here</a>.</p>';
    }
}

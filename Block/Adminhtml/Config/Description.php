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
        return '<h2>ZERO-1 Ecommerce</h2>
        <p>Sign up for a <a href="https://bit.ly/hyva-livechat">LiveChat account with a free 30 day trial here</a>.</p>';
    }
}

<?php
declare(strict_types=1);

namespace Zero1\Livechat\Block\Adminhtml\Config;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Description extends Fieldset
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        return '<h2>ZERO-1 Ecommerce</h2>
        <p>Sign up for a <a href="https://bit.ly/hyva-livechat" target="_blank" rel="noopener">LiveChat account with a free 30 day trial here</a>.</p>';
    }
}

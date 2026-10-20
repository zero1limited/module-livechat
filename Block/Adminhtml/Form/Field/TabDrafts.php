<?php
declare(strict_types=1);

namespace Zero1\Livechat\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class TabDrafts extends AbstractFieldArray
{
    protected function _prepareToRender(): void
    {
        $this->addColumn('tab_key', [
            'label' => __('Tab Key'),
            'class' => 'required-entry',
            'style' => 'width:140px',
        ]);
        $this->addColumn('message', [
            'label' => __('Draft Message'),
            'class' => 'required-entry',
            'style' => 'width:380px',
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = (string) __('Add Tab');
    }
}

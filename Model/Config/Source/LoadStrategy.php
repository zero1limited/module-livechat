<?php
declare(strict_types=1);

namespace Zero1\Livechat\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class LoadStrategy implements OptionSourceInterface
{
    public const IMMEDIATE = 'immediate';
    public const INTERACTION = 'interaction';

    public function toOptionArray(): array
    {
        return [
            ['value' => self::IMMEDIATE, 'label' => __('Immediately (async)')],
            ['value' => self::INTERACTION, 'label' => __('On first interaction (scroll / click / key / touch)')],
        ];
    }
}

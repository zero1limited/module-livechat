<?php
declare(strict_types=1);

namespace Zero1\Livechat\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;
use Zero1\Livechat\Model\WidgetCodeParser;

/**
 * Light sanity check so a mis-paste is caught in admin rather than on the storefront.
 */
class WidgetCode extends Value
{
    public function beforeSave()
    {
        $code = trim((string) $this->getValue());
        $this->setValue($code);

        if ($code !== '') {
            $parsed = (new WidgetCodeParser())->parse($code);
            if ($parsed['scripts'] === []) {
                throw new LocalizedException(__(
                    'The Web Widget HTML Code must contain the <script> block copied from LiveChat / Text.'
                ));
            }
            if (stripos($code, '__lc') === false && stripos($code, 'livechat') === false) {
                throw new LocalizedException(__(
                    'The Web Widget HTML Code does not look like a LiveChat / Text widget snippet.'
                ));
            }
        }

        return parent::beforeSave();
    }
}

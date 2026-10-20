<?php
declare(strict_types=1);

namespace Zero1\Livechat\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Zero1\Livechat\Model\Config\Source\LoadStrategy;

class Config
{
    private const PATH = 'zero1_livechat/';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var Json */
    private $json;

    public function __construct(ScopeConfigInterface $scopeConfig, Json $json)
    {
        $this->scopeConfig = $scopeConfig;
        $this->json = $json;
    }

    /**
     * Store scope lookups fall back to website then default, so values saved
     * at website scope by 1.x are still honoured.
     */
    private function value(string $path): string
    {
        return trim((string) $this->scopeConfig->getValue(self::PATH . $path, ScopeInterface::SCOPE_STORE));
    }

    private function flag(string $path): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH . $path, ScopeInterface::SCOPE_STORE);
    }

    public function isEmbedEnabled(): bool
    {
        return $this->flag('configuration/enable')
            && ($this->getWidgetCode() !== '' || $this->getLicense() !== '');
    }

    /**
     * Raw "Web Widget HTML Code" pasted from LiveChat / Text (preferred).
     */
    public function getWidgetCode(): string
    {
        return $this->value('configuration/widget_code');
    }

    /**
     * Legacy 1.x license number - only used when no widget code is set.
     */
    public function getLicense(): string
    {
        return $this->value('configuration/license_code');
    }

    public function getGroup(): string
    {
        return $this->value('configuration/group');
    }

    public function isChatBetweenGroups(): bool
    {
        return $this->flag('configuration/chat_between_groups');
    }

    public function getLoadStrategy(): string
    {
        return $this->value('configuration/load_strategy') ?: LoadStrategy::INTERACTION;
    }

    public function isPassProductContext(): bool
    {
        return $this->flag('configuration/pass_product_context');
    }

    public function isTabDraftsEnabled(): bool
    {
        return $this->flag('product_tabs/enabled') && $this->getTabDrafts() !== [];
    }

    public function isAutoOpen(): bool
    {
        return $this->flag('product_tabs/auto_open');
    }

    public function isSetSessionVariables(): bool
    {
        return $this->flag('product_tabs/set_session_variables');
    }

    /**
     * @return array<string,string> tab key => message template
     */
    public function getTabDrafts(): array
    {
        $raw = $this->value('product_tabs/drafts');
        if ($raw === '') {
            return [];
        }
        try {
            $rows = $this->json->unserialize($raw);
        } catch (\InvalidArgumentException $e) {
            return [];
        }
        $drafts = [];
        foreach ((array) $rows as $row) {
            $key = strtolower(trim((string) ($row['tab_key'] ?? '')));
            $message = trim((string) ($row['message'] ?? ''));
            if ($key !== '' && $message !== '') {
                $drafts[$key] = $message;
            }
        }
        return $drafts;
    }
}

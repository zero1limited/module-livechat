<?php
declare(strict_types=1);

namespace Zero1\Livechat\ViewModel;

use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Zero1\Livechat\Model\Config;
use Zero1\Livechat\Model\Config\Source\LoadStrategy;

class Livechat implements ArgumentInterface
{
    private const JSON_FLAGS = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    /** @var Config */
    private $config;

    /** @var CatalogHelper */
    private $catalogHelper;

    public function __construct(Config $config, CatalogHelper $catalogHelper)
    {
        $this->config = $config;
        $this->catalogHelper = $catalogHelper;
    }

    public function isEmbedEnabled(): bool
    {
        return $this->config->isEmbedEnabled();
    }

    public function isTabDraftsEnabled(): bool
    {
        return $this->config->isTabDraftsEnabled() && $this->getProduct() !== null;
    }

    public function getEmbedJson(): string
    {
        $lc = ['chat_between_groups' => $this->config->isChatBetweenGroups()];
        if ($this->config->getLicense() !== '') {
            $lc['license'] = (int) $this->config->getLicense();
        }
        if ($this->config->getOrganizationId() !== '') {
            $lc['organizationId'] = $this->config->getOrganizationId();
        }
        if ($this->config->getIntegrationName() !== '') {
            $lc['integration_name'] = $this->config->getIntegrationName();
        }
        if ($this->config->getProductName() !== '') {
            $lc['product_name'] = $this->config->getProductName();
        }
        if ($this->config->getGroup() !== '') {
            $lc['group'] = (int) $this->config->getGroup();
        }

        $product = $this->getProduct();
        if ($product && $this->config->isPassProductContext()) {
            $lc['params'] = [
                ['name' => 'product_sku', 'value' => (string) $product->getSku()],
                ['name' => 'product_name', 'value' => (string) $product->getName()],
            ];
        }

        return (string) json_encode([
            'lc' => $lc,
            'onInteraction' => $this->config->getLoadStrategy() === LoadStrategy::INTERACTION,
        ], self::JSON_FLAGS);
    }

    public function getTabDraftsJson(): string
    {
        $product = $this->getProduct();
        $replace = [
            '{product}' => $product ? (string) $product->getName() : '',
            '{sku}' => $product ? (string) $product->getSku() : '',
        ];
        $drafts = array_map(
            static function (string $msg) use ($replace): string {
                return strtr($msg, $replace);
            },
            $this->config->getTabDrafts()
        );

        return (string) json_encode([
            'drafts' => $drafts,
            'autoOpen' => $this->config->isAutoOpen(),
            'sessionVars' => $this->config->isSetSessionVariables(),
        ], self::JSON_FLAGS);
    }

    private function getProduct(): ?Product
    {
        $product = $this->catalogHelper->getProduct();
        return $product instanceof Product && $product->getId() ? $product : null;
    }
}

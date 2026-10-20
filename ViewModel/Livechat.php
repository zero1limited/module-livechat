<?php
declare(strict_types=1);

namespace Zero1\Livechat\ViewModel;

use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Zero1\Livechat\Model\Config;
use Zero1\Livechat\Model\Config\Source\LoadStrategy;
use Zero1\Livechat\Model\WidgetCodeParser;

class Livechat implements ArgumentInterface
{
    private const JSON_FLAGS = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    /** Standard LiveChat loader stub - only used for the legacy license-number fallback. */
    private const LEGACY_LOADER = ';(function(n,t,c){function i(n){return e._h?e._h.apply(null,n):e._q.push(n)}var e={_q:[],_h:null,_v:"2.0",on:function(){i(["on",c.call(arguments)])},once:function(){i(["once",c.call(arguments)])},off:function(){i(["off",c.call(arguments)])},get:function(){if(!e._h)throw new Error("[LiveChatWidget] You can\'t use getters before load.");return i(["get",c.call(arguments)])},call:function(){i(["call",c.call(arguments)])},init:function(){var n=t.createElement("script");n.async=!0,n.type="text/javascript",n.src="https://cdn.livechatinc.com/tracking.js",t.head.appendChild(n)}};!n.__lc.asyncInit&&e.init(),n.LiveChatWidget=n.LiveChatWidget||e}(window,document,[].slice))';

    /** @var Config */
    private $config;

    /** @var CatalogHelper */
    private $catalogHelper;

    /** @var WidgetCodeParser */
    private $parser;

    public function __construct(Config $config, CatalogHelper $catalogHelper, WidgetCodeParser $parser)
    {
        $this->config = $config;
        $this->catalogHelper = $catalogHelper;
        $this->parser = $parser;
    }

    public function isEmbedEnabled(): bool
    {
        return $this->config->isEmbedEnabled();
    }

    public function isTabDraftsEnabled(): bool
    {
        return $this->config->isTabDraftsEnabled() && $this->getProduct() !== null;
    }

    /**
     * Settings applied to window.__lc before the vendor snippet runs. The snippet
     * starts with `window.__lc = window.__lc || {}`, so these are preserved.
     */
    public function getEmbedJson(): string
    {
        $lc = ['chat_between_groups' => $this->config->isChatBetweenGroups()];
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

    /**
     * The pasted widget code split into scripts + noscript. Falls back to the
     * standard snippet built from the legacy 1.x license number.
     *
     * @return array{scripts: array<int, array{attributes: array<string,string>, content: string}>, noscript: string}
     */
    public function getWidgetParts(): array
    {
        $code = $this->config->getWidgetCode();
        if ($code !== '') {
            return $this->parser->parse($code);
        }

        $license = $this->config->getLicense();
        if ($license === '') {
            return ['scripts' => [], 'noscript' => ''];
        }

        return [
            'scripts' => [[
                'attributes' => [],
                'content' => 'window.__lc = window.__lc || {};' . "\n"
                    . 'window.__lc.license = ' . (int) $license . ';' . "\n"
                    . self::LEGACY_LOADER,
            ]],
            'noscript' => '',
        ];
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

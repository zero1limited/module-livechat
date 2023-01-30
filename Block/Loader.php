<?php

namespace Zero1\Livechat\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class Loader extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * @param Template\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param AssetRepository $assetRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        AssetRepository $assetRepository,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->assetRepository = $assetRepository;

        parent::__construct($context, $data);
    }

    /**
     * @return string|null
     */
    public function _toHtml()
    {
        if(!$this->getIsEnabled() || !$this->getLicenseCode()){
            return null;
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getIsEnabled()
    {
        return $this->scopeConfig->getValue('zero1_livechat/configuration/enable');
    }

    /**
     * @return string
     */
    public function getLicenseCode()
    {
        return $this->scopeConfig->getValue('zero1_livechat/configuration/license_code');
    }

    /**
     * @return string
     */
    public function getLivechatJsUrl()
    {
        return $this->assetRepository->getUrl('Zero1_Livechat::js/livechat.js');
    }
}

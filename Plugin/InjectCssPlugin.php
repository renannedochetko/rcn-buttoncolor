<?php
namespace RCN\ButtonColor\Plugin;

use RCN\ButtonColor\Block\InjectCss;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class InjectCssPlugin
{
    private const XML_PATH_ENABLED = 'rcn_button_color/button/enabled';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Oculta o bloco se o módulo estiver desabilitado na store atual.
     *
     * @param InjectCss $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(InjectCss $subject, string $result): string
    {
        $storeId = $this->storeManager->getStore()->getId();

        $isEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORES,
            $storeId
        );

        return $isEnabled ? $result : '';
    }
}

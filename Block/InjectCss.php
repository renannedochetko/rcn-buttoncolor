<?php
namespace RCN\ButtonColor\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class InjectCss extends Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Indica que a saída do bloco depende da store view.
     *
     * @var bool
     */
    protected $_isScopePrivate = true;

    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }

    /**
     * Define key de cache por store view.
     *
     * @return array
     */
    public function getCacheKeyInfo(): array
    {
        return [
            'RCN_BUTTON_COLOR',
            $this->_storeManager->getStore()->getId()
        ];
    }

    /**
     * Gera CSS inline com a cor do botão, se configurada.
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        $storeId = $this->_storeManager->getStore()->getId();

        $color = $this->scopeConfig->getValue(
            'rcn_button_color/button/color',
            ScopeInterface::SCOPE_STORES,
            $storeId
        );

        if ($color) {
            $color = ltrim($color, '#');
            if (preg_match('/^[a-fA-F0-9]{3,6}$/', $color)) {
                return '<style>.action.primary, .action-primary, .action.button { background-color: #' . $color . ' !important; border-color: #' . $color . ' !important; }</style>';
            }
        }

        return '';
    }
}

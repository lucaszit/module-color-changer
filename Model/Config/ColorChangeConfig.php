<?php
/**
 * Copyright © 2025 Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\ColorChanger\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Provides access to configuration settings for the Color Changer module.
 */
class ColorChangeConfig
{
    public const string XML_PATH_ENABLED = 'color_changer/general/enabled';
    public const string XML_PATH_COLOR_HEX = 'color_changer/general/color_hex';
    public const string XML_PATH_TAGS = 'color_changer/general/tags';

    /**
     * ColorChangeConfig Constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    /**
     * Checks if the Color Changer feature is enabled for a specific store.
     *
     * @param int $storeId
     * @return bool
     */
    public function isEnabled(int $storeId): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Retrieves the hex color value from the configuration for a specific store.
     *
     * @param int $storeId
     * @return string
     */
    public function getHexColor(int $storeId): string
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_COLOR_HEX, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Retrieves the CSS tags from the configuration for a specific store.
     *
     * @param int $storeId
     * @return string
     */
    public function getCssTags(int $storeId): string
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_TAGS, ScopeInterface::SCOPE_STORE, $storeId);
    }
}

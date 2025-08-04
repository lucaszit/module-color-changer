<?php
/**
 * Copyright © 2025 Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\ColorChanger\Model\Service;

use Exception;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Store\Model\StoreManagerInterface;
use Hibrido\ColorChanger\Model\Config\ColorChangeConfig;

/**
 * Service to handle color changes for the Color Changer module.
 * This service is responsible for changing the button color for a specific store view.
 */
class ColorChangeService
{
    /**
     * ColorChangeService constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param File $file
     * @param ColorChangeConfig $colorChangeConfig
     */
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly File $file,
        private readonly ColorChangeConfig $colorChangeConfig
    ) {}

    /**
     * Changes the button color for a store view.
     *
     * @param string $hex
     * @param int $storeId
     * @throws Exception
     */
    public function changeColor(string $hex, int $storeId): void
    {
        $this->validateStore($storeId);
        $this->validateModuleEnabled($storeId);
        $hex = $this->validateAndFormatHex($hex);
        $tags = $this->getCssTags($storeId);
        $cssContent = $this->generateCssContent($tags, $hex, $storeId);
        $this->writeCssFile($cssContent, $storeId);
    }

    /**
     * Validates if the store ID exists.
     *
     * @param int $storeId
     * @throws NoSuchEntityException
     */
    private function validateStore(int $storeId): void
    {
        $this->storeManager->getStore($storeId);
    }

    /**
     * Validates if the Color Changer module is enabled for the specified store.
     *
     * @param int $storeId
     * @throws Exception
     */
    private function validateModuleEnabled(int $storeId): void
    {
        if (!$this->colorChangeConfig->isEnabled($storeId)) {
            throw new Exception('Module needs to be enabled.');
        }
    }

    /**
     * Validates the HEX color format and ensures it starts with a '#'.
     *
     * @param string $hex
     * @return string
     * @throws Exception
     */
    private function validateAndFormatHex(string $hex): string
    {
        if (!preg_match('/^#[0-9A-Fa-f]{6}$|^[0-9A-Fa-f]{6}$/', $hex)) {
            throw new Exception('Invalid Color format. Use HEX, ex: 000000');
        }
        return str_starts_with($hex, '#') ? $hex : '#' . $hex;
    }

    /**
     * Retrieves the CSS tags configured in the admin for the specified store.
     *
     * @param int $storeId
     * @return string
     * @throws Exception
     */
    private function getCssTags(int $storeId): string
    {
        $tags = $this->colorChangeConfig->getCssTags($storeId);
        if (empty($tags)) {
            throw new Exception('No CSS tag configured in the admin.');
        }
        return $tags;
    }

    /**
     * Generates the CSS content for the specified tags and color.
     *
     * @param string $tags
     * @param string $hex
     * @param int $storeId
     * @return string
     */
    private function generateCssContent(string $tags, string $hex, int $storeId): string
    {
        return sprintf(
            "/** ColorChanger CSS - StoreView %d */\n%s { background-color: %s !important; }\n/* End ColorChanger */\n",
            $storeId,
            $tags,
            $hex
        );
    }

    /**
     * Writes the generated CSS content to a file for the specified store.
     *
     * @param string $cssContent
     * @param int $storeId
     * @throws FileSystemException
     */
    private function writeCssFile(string $cssContent, int $storeId): void
    {
        $cssDir = BP . '/pub/media/colorchanger';
        $cssFile = $cssDir . '/store_' . $storeId . '.css';

        if (!$this->file->isExists($cssDir)) {
            $this->file->createDirectory($cssDir, 0775);
        }
        $this->file->filePutContents($cssFile, $cssContent);
    }
}

<?php
/**
 * Copyright © 2025 Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\ColorChanger\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Hibrido\ColorChanger\Model\Service\ColorChangeService;
use Hibrido\ColorChanger\Model\Config\ColorChangeConfig;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Observer to handle the saving of configuration changes related to the button color.
 * This observer listens for configuration save events and applies the color change if enabled.
 */
class SaveConfig implements ObserverInterface
{
    private const string ERROR_LOG_MESSAGE = 'Error while changing button color in admin panel: ';

    /**
     * SaveConfig constructor.
     *
     * @param ColorChangeService $colorChangeService
     * @param ColorChangeConfig $colorChangeConfig
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        private readonly ColorChangeService $colorChangeService,
        private readonly ColorChangeConfig $colorChangeConfig,
        private readonly LoggerInterface $logger,
        private readonly StoreManagerInterface $storeManagerInterface
    ) {}

    /**
     * Executes the observer to change the button color when configuration is saved.
     *
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer): void
    {
        $storeId = (int) $this->storeManagerInterface->getStore()->getId();

        if (!$this->colorChangeConfig->isEnabled($storeId)) {
            return;
        }

        $hexColor = $this->colorChangeConfig->getHexColor($storeId);
        $this->applyColorChange($hexColor, $storeId);
    }

    /**
     * Applies the color change by calling the ColorChangeService.
     *
     * @param string $hexColor
     * @param int $storeId
     */
    private function applyColorChange(string $hexColor, int $storeId): void
    {
        try {
            $this->colorChangeService->changeColor($hexColor, $storeId);
        } catch (Exception $e) {
            $this->logger->error(self::ERROR_LOG_MESSAGE . $e->getMessage(), ['exception' => $e]);
        }
    }
}

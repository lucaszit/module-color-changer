<?php
/**
 * Copyright © 2025 Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\ColorChanger\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Observer to add custom CSS for the color changer feature.
 * This observer listens to the event where the page configuration is being set up
 * and adds a custom CSS file based on the current store view.
 */
readonly class AddColorCss implements ObserverInterface
{
    /**
     * AddColorCss Constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param PageConfig $pageConfig
     */
    public function __construct(
        private StoreManagerInterface $storeManager,
        private PageConfig            $pageConfig
    ) {}

    /**
     * Execute method to add the custom CSS file to the page configuration.
     *
     * @param Observer $observer
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer): void
    {
        $storeId = $this->storeManager->getStore()->getId();
        $cssPath = '/media/colorchanger/store_' . $storeId . '.css';
        $pubPath = BP . '/pub' . $cssPath;
        if (file_exists($pubPath)) {
            $this->pageConfig->addRemotePageAsset($cssPath, 'css', ['attributes' => ['rel' => 'stylesheet', 'type' => 'text/css']]);
        }
    }
}

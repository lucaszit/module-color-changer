<?php
/**
 * Copyright © 2025 Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\ColorChanger\Test\Unit\Observer;

use Hibrido\ColorChanger\Observer\AddColorCss;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;

/**
 * Class AddColorCssTest for Unit Tests
 *
 * @coversDefaultClass \Hibrido\ColorChanger\Observer\AddColorCss
 */
class AddColorCssTest extends TestCase
{
    /** @var MockObject|StoreManagerInterface */
    private StoreManagerInterface $storeManagerMock;

    /** @var MockObject|PageConfig */
    private PageConfig $pageConfigMock;

    /** @var MockObject|addColorCss */
    private AddColorCss $addColorCss;

    /**
     * Setup Tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        if (!defined('BP')) {
            define('BP', '/tmp');
        }

        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->pageConfigMock = $this->createMock(PageConfig::class);

        $this->addColorCss = new AddColorCss(
            $this->storeManagerMock,
            $this->pageConfigMock
        );
    }

    /**
     * Prepare Store Mock and Paths
     *
     * @param int $storeId
     * @return array
     */
    private function prepareStoreMockAndPaths(int $storeId): array
    {
        $cssPath = '/media/colorchanger/store_' . $storeId . '.css';
        $pubPath = BP . '/pub' . $cssPath;

        $storeMock = $this->createMock(Store::class);
        $storeMock->method('getId')->willReturn($storeId);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);

        return [$cssPath, $pubPath];
    }

    /**
     * TestCanCreate
     *
     * @covers ::__construct
     *
     * return void
     */
    public function testCanCreate(): void
    {
        $this->assertInstanceOf(addColorCss::class, $this->addColorCss);
    }

    /**
     * Test execute method when the CSS file exists.
     *
     * @covers ::execute
     *
     * @return void
     */
    public function testExecuteAddsCssWhenFileExists(): void
    {
        $storeId = 1;
        [$cssPath, $pubPath] = $this->prepareStoreMockAndPaths($storeId);

        $directory = dirname($pubPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $this->assertTrue(touch($pubPath));
        $this->pageConfigMock->expects($this->once())
            ->method('addRemotePageAsset')
            ->with($cssPath, 'css', ['attributes' => ['rel' => 'stylesheet', 'type' => 'text/css']]);

        $this->addColorCss->execute($this->createMock(Observer::class));
        unlink($pubPath);
    }

    /**
     * Test execute method when the CSS file does not exist.
     *
     * @covers ::execute
     *
     * @return void
     */
    public function testExecuteDoesNotAddCssWhenFileDoesNotExist(): void
    {
        $storeId = 1;
        [$cssPath, $pubPath] = $this->prepareStoreMockAndPaths($storeId);

        $this->pageConfigMock->expects($this->never())
            ->method('addRemotePageAsset');

        $this->addColorCss->execute($this->createMock(Observer::class));
    }
}

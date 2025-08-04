<?php
/**
 * Copyright © 2025 Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\ColorChanger\Test\Unit\Model\Service;

use Exception;
use Hibrido\ColorChanger\Model\Config\ColorChangeConfig;
use Hibrido\ColorChanger\Model\Service\ColorChangeService;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;

/**
 * Class ColorChangeServiceTest for Unit Tests
 *
 * @coversDefaultClass \Hibrido\ColorChanger\Model\Service\ColorChangeService
 */
class ColorChangeServiceTest extends TestCase
{
    /** @var MockObject|StoreManagerInterface */
    private StoreManagerInterface $storeManagerMock;

    /** @var MockObject|File */
    private File $fileMock;

    /** @var MockObject|ColorChangeConfig */
    private ColorChangeConfig$colorChangeConfigMock;

    /** @var MockObject|colorChangeService */
    private ColorChangeService $colorChangeService;

    /**
     * Setup Tests
     *
     * @return void
     * @throws FileSystemException
     */
    protected function setUp(): void
    {
        if (!defined('BP')) {
            define('BP', '/tmp');
        }

        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->fileMock = $this->createMock(File::class);
        $this->colorChangeConfigMock = $this->createMock(ColorChangeConfig::class);

        $this->colorChangeService = new ColorChangeService(
            $this->storeManagerMock,
            $this->fileMock,
            $this->colorChangeConfigMock
        );
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
        $this->assertInstanceOf(colorChangeService::class, $this->colorChangeService);
    }

    /**
     * Test changeColor method with valid parameters
     *
     * @covers ::changeColor
     *
     * @return void
     */
    public function testChangeColorSuccess(): void
    {
        $storeId = 1;
        $hex = '#FFFFFF';
        $tags = '.button';

        $storeMock = $this->createMock(Store::class);
        $this->storeManagerMock->method('getStore')->with($storeId)->willReturn($storeMock);

        $this->colorChangeConfigMock->method('isEnabled')->with($storeId)->willReturn(true);
        $this->colorChangeConfigMock->method('getCssTags')->with($storeId)->willReturn($tags);

        $this->fileMock->method('isExists')->willReturn(false);
        $this->fileMock->expects($this->once())->method('createDirectory');
        $this->fileMock->expects($this->once())->method('filePutContents');

        $this->colorChangeService->changeColor($hex, $storeId);
    }

    /**
     * Test changeColor method with invalid parameters
     *
     * @covers ::changeColor
     *
     * @return void
     */
    public function testChangeColorThrowsExceptionWhenStoreNotFound(): void
    {
        $storeId = 999;

        $this->storeManagerMock->method('getStore')->with($storeId)
            ->willThrowException(new NoSuchEntityException(__('Store not found')));

        $this->expectException(NoSuchEntityException::class);
        $this->colorChangeService->changeColor('#FFFFFF', $storeId);
    }

    /**
     * Test changeColor method when module is disabled
     *
     * @covers ::changeColor
     *
     * @return void
     */
    public function testChangeColorThrowsExceptionWhenModuleDisabled(): void
    {
        $storeId = 1;

        $this->storeManagerMock->method('getStore')->with($storeId)->willReturn($this->createMock(Store::class));
        $this->colorChangeConfigMock->method('isEnabled')->with($storeId)->willReturn(false);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Module needs to be enabled.');
        $this->colorChangeService->changeColor('#FFFFFF', $storeId);
    }

    /**
     * Test changeColor method with invalid hex color
     *
     * @covers ::changeColor
     *
     * @return void
     */
    public function testChangeColorThrowsExceptionForInvalidHex(): void
    {
        $storeId = 1;
        $invalidHex = 'ZZZZZZ';

        $this->storeManagerMock->method('getStore')->with($storeId)->willReturn($this->createMock(Store::class));
        $this->colorChangeConfigMock->method('isEnabled')->with($storeId)->willReturn(true);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Color format. Use HEX, ex: 000000');
        $this->colorChangeService->changeColor($invalidHex, $storeId);
    }

    /**
     * Test changeColor method when no CSS tags are configured
     *
     * @covers ::changeColor
     *
     * @return void
     */
    public function testChangeColorThrowsExceptionWhenNoCssTagsConfigured(): void
    {
        $storeId = 1;
        $hex = '#FFFFFF';

        $this->storeManagerMock->method('getStore')->with($storeId)->willReturn($this->createMock(Store::class));
        $this->colorChangeConfigMock->method('isEnabled')->with($storeId)->willReturn(true);
        $this->colorChangeConfigMock->method('getCssTags')->with($storeId)->willReturn('');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No CSS tag configured in the admin.');
        $this->colorChangeService->changeColor($hex, $storeId);
    }
}

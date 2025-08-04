<?php
/**
 * Copyright © 2025 Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\ColorChanger\Test\Unit\Observer;

use Exception;
use Hibrido\ColorChanger\Model\Config\ColorChangeConfig;
use Hibrido\ColorChanger\Model\Service\ColorChangeService;
use Hibrido\ColorChanger\Observer\SaveConfig;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class SaveConfigTest for Unit Tests
 *
 * @coversDefaultClass \Hibrido\ColorChanger\Observer\SaveConfig
 */
class SaveConfigTest extends TestCase
{
    /** @var MockObject|ColorChangeService */
    private ColorChangeService $colorChangeServiceMock;

    /** @var MockObject|ColorChangeConfig */
    private ColorChangeConfig $colorChangeConfigMock;

    /** @var MockObject|LoggerInterface */
    private LoggerInterface $loggerMock;

    /** @var MockObject|StoreManagerInterface */
    private StoreManagerInterface $storeManagerMock;

    /** @var SaveConfig */
    private SaveConfig $saveConfig;

    /**
     * Setup Tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->colorChangeServiceMock = $this->createMock(ColorChangeService::class);
        $this->colorChangeConfigMock = $this->createMock(ColorChangeConfig::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $this->saveConfig = new SaveConfig(
            $this->colorChangeServiceMock,
            $this->colorChangeConfigMock,
            $this->loggerMock,
            $this->storeManagerMock
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
        $this->assertInstanceOf(saveConfig::class, $this->saveConfig);
    }

    /**
     * TestExecuteDoesNothingWhenModuleDisabled
     *
     * @covers ::execute
     *
     * @return void
     */
    public function testExecuteDoesNothingWhenModuleDisabled(): void
    {
        $storeMock = $this->createMock(Store::class);
        $storeMock->method('getId')->willReturn(1);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);

        $this->colorChangeConfigMock->method('isEnabled')->with(1)->willReturn(false);
        $observerMock = $this->createMock(Observer::class);
        $this->colorChangeServiceMock->expects($this->never())->method('changeColor');

        $this->saveConfig->execute($observerMock);
    }

    /**
     * TestExecuteAppliesColorChangeWhenModuleEnabled
     *
     * @covers ::execute
     *
     * @return void
     */
    public function testExecuteAppliesColorChangeWhenModuleEnabled(): void
    {
        $storeMock = $this->createMock(Store::class);
        $storeMock->method('getId')->willReturn(1);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);

        $this->colorChangeConfigMock->method('isEnabled')->with(1)->willReturn(true);
        $this->colorChangeConfigMock->method('getHexColor')->with(1)->willReturn('#FFFFFF');
        $observerMock = $this->createMock(Observer::class);

        $this->colorChangeServiceMock->expects($this->once())
            ->method('changeColor')
            ->with('#FFFFFF', 1);

        $this->saveConfig->execute($observerMock);
    }

    /**
     * TestExecuteLogsErrorWhenExceptionThrown
     *
     * @covers ::execute
     *
     * @return void
     */
    public function testExecuteLogsErrorWhenExceptionThrown(): void
    {
        $storeMock = $this->createMock(Store::class);
        $storeMock->method('getId')->willReturn(1);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);

        $this->colorChangeConfigMock->method('isEnabled')->with(1)->willReturn(true);
        $this->colorChangeConfigMock->method('getHexColor')->with(1)->willReturn('#FFFFFF');
        $observerMock = $this->createMock(Observer::class);

        $this->colorChangeServiceMock->method('changeColor')
            ->willThrowException(new Exception('Test exception'));

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with('Error while changing button color in admin panel: Test exception', $this->anything());

        $this->saveConfig->execute($observerMock);
    }
}

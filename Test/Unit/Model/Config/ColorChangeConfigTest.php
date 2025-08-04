<?php
/**
 * Copyright © 2025 Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\ColorChanger\Test\Unit\Model\Config;

use Hibrido\ColorChanger\Model\Config\ColorChangeConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class ColorChangeConfigTest for Unit Tests
 *
 * @coversDefaultClass \Hibrido\ColorChanger\Model\Config\ColorChangeConfig
 */
class ColorChangeConfigTest extends TestCase
{
    /** @var MockObject|ScopeConfigInterface */
    private ScopeConfigInterface $scopeConfigMock;

    /** @var MockObject|ColorChangeConfig */
    private ColorChangeConfig $colorChangeConfig;

    /**
     * Setup Tests
     *
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->colorChangeConfig = new ColorChangeConfig($this->scopeConfigMock);
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
        $this->assertInstanceOf(colorChangeConfig::class, $this->colorChangeConfig);
    }

    /**
     * TestIsEnabled
     *
     * @covers ::isEnabled
     *
     * return void
     */
    public function testIsEnabled(): void
    {
        $storeId = 1;
        $this->scopeConfigMock->method('isSetFlag')
            ->with(ColorChangeConfig::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(true);

        $result = $this->colorChangeConfig->isEnabled($storeId);

        $this->assertTrue($result);
    }

    /**
     * TestGetHexColor
     *
     * @covers ::getHexColor
     *
     * return void
     */
    public function testGetHexColor(): void
    {
        $storeId = 1;
        $hexColor = '#FFFFFF';
        $this->scopeConfigMock->method('getValue')
            ->with(ColorChangeConfig::XML_PATH_COLOR_HEX, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($hexColor);

        $result = $this->colorChangeConfig->getHexColor($storeId);

        $this->assertEquals($hexColor, $result);
    }

    /**
     * TestGetCssTags
     *
     * @covers ::getCssTags
     *
     * return void
     */
    public function testGetCssTags(): void
    {
        $storeId = 1;
        $cssTags = '.button { color: #FFFFFF; }';
        $this->scopeConfigMock->method('getValue')
            ->with(ColorChangeConfig::XML_PATH_TAGS, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($cssTags);

        $result = $this->colorChangeConfig->getCssTags($storeId);

        $this->assertEquals($cssTags, $result);
    }
}

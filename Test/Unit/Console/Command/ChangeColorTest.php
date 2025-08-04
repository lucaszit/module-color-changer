<?php
/**
 * Copyright © 2025 Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\ColorChanger\Test\Unit\Console\Command;

use Exception;
use Hibrido\ColorChanger\Console\Command\ChangeColor;
use Hibrido\ColorChanger\Model\Service\ColorChangeService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ChangeColorTest for Unit Tests
 *
 * @coversDefaultClass \Hibrido\ColorChanger\Console\Command\ChangeColor
 */
class ChangeColorTest extends TestCase
{
    /** @var MockObject|ChangeColor */
    private ChangeColor $command;

    /** @var MockObject|ColorChangeService */
    private ColorChangeService $colorChangeServiceMock;

    /** @var MockObject|InputInterface */
    private InputInterface $inputMock;

    /** @var MockObject|OutputInterface */
    private OutputInterface $outputMock;

    /**
     * Setup Tests
     *
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->colorChangeServiceMock = $this->createMock(ColorChangeService::class);
        $this->inputMock = $this->createMock(InputInterface::class);
        $this->outputMock = $this->createMock(OutputInterface::class);

        $this->command = new ChangeColor($this->colorChangeServiceMock);
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
        $this->assertInstanceOf(Command::class, $this->command);
    }

    /**
     * TestExecuteSuccess
     *
     * @covers ::execute
     *
     * return void
     */
    public function testExecuteSuccess(): void
    {
        $hex = '#FFFFFF';
        $storeId = 1;

        $this->inputMock->method('getArgument')
            ->willReturnMap([
                ['hex', $hex],
                ['store_id', $storeId],
            ]);

        $this->colorChangeServiceMock
            ->expects($this->once())
            ->method('changeColor')
            ->with($hex, $storeId);

        $this->outputMock
            ->expects($this->once())
            ->method('writeln')
            ->with('<info>Button color changed to ' . $hex . ' in store view ' . $storeId . '</info>');

        $result = $this->command->execute($this->inputMock, $this->outputMock);

        $this->assertEquals(Command::SUCCESS, $result);
    }

    /**
     * TestExecuteFailure
     *
     * @covers ::execute
     *
     * return void
     */
    public function testExecuteFailure(): void
    {
        $hex = '#FFFFFF';
        $storeId = 1;
        $errorMessage = 'An error occurred';

        $this->inputMock->method('getArgument')
            ->willReturnMap([
                ['hex', $hex],
                ['store_id', $storeId],
            ]);

        $this->colorChangeServiceMock
            ->method('changeColor')
            ->willThrowException(new Exception($errorMessage));

        $this->outputMock
            ->expects($this->once())
            ->method('writeln')
            ->with('<error>' . $errorMessage . '</error>');

        $result = $this->command->execute($this->inputMock, $this->outputMock);

        $this->assertEquals(Command::FAILURE, $result);
    }
}

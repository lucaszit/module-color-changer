<?php
/**
 * Copyright © 2025 Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\ColorChanger\Console\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Hibrido\ColorChanger\Model\Service\ColorChangeService;

/**
 * Command to change the button color for a specific store view.
 */
class ChangeColor extends Command
{
    public const string COMMAND_NAME = 'color:change';

    /**
     * ColorChange constructor.
     *
     * @param ColorChangeService $colorChangeService
     */
    public function __construct(
        private readonly ColorChangeService $colorChangeService
    ) {
        parent::__construct();
    }

    /**
     * Configure the command with its name, description, and arguments.
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Changes the button color for a store view')
            ->addArgument('hex', InputArgument::REQUIRED, 'Button HEX color')
            ->addArgument('store_id', InputArgument::REQUIRED, 'Store view ID');
    }

    /**
     * Execute the command to change the button color.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $hex = (string) $input->getArgument('hex');
        $storeId = (int) $input->getArgument('store_id');

        try {
            $this->colorChangeService->changeColor($hex, $storeId);
        } catch (Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $output->writeln(
            '<info>Button color changed to ' . $hex . ' in store view ' . $storeId . '</info>'
        );
        return Command::SUCCESS;
    }
}

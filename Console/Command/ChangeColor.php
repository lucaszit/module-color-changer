<?php
/**
 * Copyright © 2025 Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\ColorChanger\Console\Command;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to change the color of buttons in a specific store view.
 * This command allows administrators to set a custom HEX color for buttons
 * and actions in the Magento store, affecting only the specified store view.
 */
class ChangeColor extends Command
{
    /**
     * ChangeColor Constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param File $file
     */
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly File $file,
    ) {
        parent::__construct();
    }

    /**
     * Configure the command with its name, description, and arguments.
     */
    protected function configure(): void
    {
        $this->setName('color:change')
            ->setDescription('Altera a cor dos botões para uma store-view')
            ->addArgument('hex', InputArgument::REQUIRED, 'Cor HEX do botão')
            ->addArgument('store_id', InputArgument::REQUIRED, 'ID da store-view');
    }

    /**
     * Execute the command to change the button color.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws FileSystemException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hex = $input->getArgument('hex');
        $storeId = $input->getArgument('store_id');

        // Validação do HEX
        if (!preg_match('/^#[0-9A-Fa-f]{6}$|^[0-9A-Fa-f]{6}$/', $hex)) {
            $output->writeln('<error>Formato de cor inválido. Use HEX, ex: 000000 ou #000000</error>');
            return Command::FAILURE;
        }
        if (strpos($hex, '#') !== 0) {
            $hex = '#' . $hex;
        }

        // Validação da store-view
        try {
            $store = $this->storeManager->getStore($storeId);
        } catch (\Exception $e) {
            $output->writeln('<error>Store-view não encontrada.</error>');
            return Command::FAILURE;
        }

        // Caminho do CSS customizado
        $cssDir = BP . '/pub/media/colorchanger';
        $cssFile = $cssDir . '/store_' . $storeId . '.css';
        if (!$this->file->isExists($cssDir)) {
            try {
                $this->file->createDirectory($cssDir, 0775);
            } catch (\Exception $e) {
                $output->writeln('<error>Não foi possível criar o diretório ' . $cssDir . '. Verifique permissões.</error>');
                return Command::FAILURE;
            }
        }
        // Conteúdo CSS para alterar apenas o background-color dos botões e ações
        $cssContent = "/** ColorChanger CSS - StoreView $storeId */\n";
        $cssContent .= ".btn, button, input[type='button'], input[type='submit'], .action.primary, .action.secondary, .action.save, .action.cancel, .action.close, .action.delete, .action.add, .action.more, .action.less, .action.checkout, .action.back, .action.next, .action.previous, .action.search, .action.filter, .action.apply, .action.reset, .block-promo .action, .block-promo .action.more, .block-promo .action.button, .block-promo .action.icon, .block-promo .action.primary, .block-promo .action.secondary, .block-promo .action.add, .block-promo .action.checkout, .block-promo .action.save, .block-promo .action.cancel, .block-promo .action.close, .block-promo .action.delete, .block-promo .action.next, .block-promo .action.previous, .block-promo .action.search, .block-promo .action.filter, .block-promo .action.apply, .block-promo .action.reset { background-color: $hex !important; }\n";
        $cssContent .= "a.btn, a.button, a.action, a.action.primary, a.action.secondary, a.action.save, a.action.cancel, a.action.close, a.action.delete, a.action.add, a.action.more, a.action.less, a.action.checkout, a.action.back, a.action.next, a.action.previous, a.action.search, a.action.filter, a.action.apply, a.action.reset { background-color: $hex !important; }\n";
        $cssContent .= "/* Fim ColorChanger */\n";
        $this->file->filePutContents($cssFile, $cssContent);

        $output->writeln('<info>Cor dos botões alterada para ' . $hex . ' na store-view ' . $storeId . '.</info>');
        return Command::SUCCESS;
    }
}

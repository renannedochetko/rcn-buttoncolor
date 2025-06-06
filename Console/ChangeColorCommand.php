<?php

namespace RCN\ButtonColor\Console;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\Cache\TypeListInterface;

class ChangeColorCommand extends Command
{
    /**
     * Configuração do caminho para salvar a cor do botão.
     */
    private const COLOR_CONFIG_PATH = 'rcn_button_color/button/color';

    /**
     * @var WriterInterface
     */
    private WriterInterface $configWriter;

    /**
     * @var StoreRepositoryInterface
     */
    private StoreRepositoryInterface $storeRepository;

    /**
     * @var TypeListInterface
     */
    private TypeListInterface $cacheTypeList;

    /**
     * @param WriterInterface $configWriter
     * @param StoreRepositoryInterface $storeRepository
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        WriterInterface $configWriter,
        StoreRepositoryInterface $storeRepository,
        TypeListInterface $cacheTypeList
    ) {
        $this->configWriter = $configWriter;
        $this->storeRepository = $storeRepository;
        $this->cacheTypeList = $cacheTypeList;
        parent::__construct();
    }

    /**
     * Configuração do comando com argumentos.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('color:change')
            ->setDescription('Change button color for specific store-view')
            ->addArgument('hex', InputArgument::REQUIRED, 'HEX Color Code (e.g., 000 or ffcc00)')
            ->addArgument('store_id', InputArgument::REQUIRED, 'Store View ID');
    }

    /**
     * Execução do comando para atualizar a cor do botão e para limpar os caches.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hex = strtolower(trim($input->getArgument('hex')));
        $storeId = $input->getArgument('store_id');

        if (!preg_match('/^([a-f0-9]{3}|[a-f0-9]{6})$/', $hex)) {
            $output->writeln("<error>Invalid HEX color. Use 3 or 6 characters, e.g., 000 or ffcc00 (no #).</error>");
            return Command::FAILURE;
        }

        try {
            $this->storeRepository->getById($storeId);
        } catch (\Exception $e) {
            $output->writeln("<error>Store view ID $storeId not found.</error>");
            return Command::FAILURE;
        }

        $normalizedHex = '#' . $hex;

        try {
            $this->configWriter->save(
                self::COLOR_CONFIG_PATH,
                $normalizedHex,
                ScopeInterface::SCOPE_STORES,
                $storeId
            );

            $typesToClean = ['config', 'layout', 'block_html', 'full_page'];
            foreach ($typesToClean as $type) {
                $this->cacheTypeList->cleanType($type);
            }

            $output->writeln("<info>Button color updated to $normalizedHex for store view $storeId.</info>");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>Something went wrong while saving config or clearing cache: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}

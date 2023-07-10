<?php

namespace App\Command;

use App\Service\CatalogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import:products',
    description: 'Import Products from CSV',
)]
class ImportProductsCommand extends Command
{

    public function __construct(
        private CatalogService $catalogService,
        private EntityManagerInterface $entityManagerInterface
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::OPTIONAL, 'Filename of CSV to Import')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        if ($file) {
            $io->note(sprintf('You passed an argument: %s', $file));
        }

        $line = 0;

        $fh = fopen($file, "r");
        while (false !== ($data = fgetcsv($fh))) {
            if ($line++ == 0) { continue; }
            $this->catalogService->addOrUpdateProduct([
                'sku' => $data[0],
                'name' => $data[1],
                'releaseDate' => new \DateTimeImmutable($data[2]),
                'manufacturer' => $data[4],
                'type' => $data[5],
                'categories' => explode('|', $data[6])
            ]);
        }

        $io->success(sprintf('Products Successfully Imported %d Items', $line));

        return Command::SUCCESS;
    }
}

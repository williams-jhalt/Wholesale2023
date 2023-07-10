<?php

namespace App\Command;

use App\Entity\ProductType;
use App\Repository\ProductTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import:types',
    description: 'Import Product Types from CSV',
)]
class ImportTypesCommand extends Command
{

    public function __construct(
        private ProductTypeRepository $productTypeRepository,
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
            $type = $this->productTypeRepository->findOneByCode($data[0]);
            if ($type == null) {
                $type = new ProductType();
                $type->setCode($data[0]);
            }
            $type->setName($data[1]);
            $this->entityManagerInterface->persist($type);
        }

        $this->entityManagerInterface->flush();

        $io->success(sprintf('Types Successfully Imported %d Items', $line));

        return Command::SUCCESS;
    }
}

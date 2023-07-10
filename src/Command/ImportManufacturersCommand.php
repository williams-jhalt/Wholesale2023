<?php

namespace App\Command;

use App\Entity\ProductManufacturer;
use App\Repository\ProductManufacturerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import:manufacturers',
    description: 'Import Manufacturers from CSV',
)]
class ImportManufacturersCommand extends Command
{

    public function __construct(
        private ProductManufacturerRepository $productManufacturerRepository,
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
            $manufacturer = $this->productManufacturerRepository->findOneByCode($data[0]);
            if ($manufacturer == null) {
                $manufacturer = new ProductManufacturer();
                $manufacturer->setCode($data[0]);
            }
            $manufacturer->setName($data[1]);
            $this->entityManagerInterface->persist($manufacturer);
        }

        $this->entityManagerInterface->flush();

        $io->success(sprintf('Manufacturers Successfully Imported %d Items', $line));

        return Command::SUCCESS;
    }
}

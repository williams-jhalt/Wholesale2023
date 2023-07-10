<?php

namespace App\Command;

use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import:categories',
    description: 'Import Categories from CSV',
)]
class ImportCategoriesCommand extends Command
{

    public function __construct(
        private ProductCategoryRepository $productCategoryRepository,
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
            $category = $this->productCategoryRepository->findOneByCode($data[0]);
            if ($category == null) {
                $category = new ProductCategory();
                $category->setCode($data[0]);
            }
            $category->setName($data[1]);
            $this->entityManagerInterface->persist($category);
        }

        $this->entityManagerInterface->flush();

        $io->success(sprintf('Categories Successfully Imported %d Items', $line));

        return Command::SUCCESS;
    }
}

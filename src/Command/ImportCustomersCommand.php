<?php

namespace App\Command;

use App\Service\CustomerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import:customers',
    description: 'Import Customers from ERP',
)]
class ImportCustomersCommand extends Command
{

    public function __construct(
        private CustomerService $customerService
    ) {
        parent::__construct();
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->customerService->loadCustomersFromErp();

        return Command::SUCCESS;
    }
}

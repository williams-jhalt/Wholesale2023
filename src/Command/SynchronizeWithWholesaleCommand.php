<?php

namespace App\Command;

use App\Service\WholesaleSynchronizationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sync:wholesale',
    description: 'Synchronize with Wholesale Website',
)]
class SynchronizeWithWholesaleCommand extends Command
{

    public function __construct(
        private WholesaleSynchronizationService $wholesaleSynchronizationService
    ) {
        parent::__construct();
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->wholesaleSynchronizationService->sync();

        return Command::SUCCESS;
    }
}

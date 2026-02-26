<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserCommandsRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveExpiredCartsCommand extends Command
{
    protected static $defaultName = 'jtwc:remove-expired-carts';

    /**
     * EntityManager
     *
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Fetch all expired carts
     *
     * @var UserCommandsRepository
     */
    private $userCommandRepository;

    public function __construct(EntityManagerInterface $manager, UserCommandsRepository $userCommandRepository)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->userCommandRepository = $userCommandRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Removes carts that have been inactive for a defined period')
            ->addArgument('days', InputArgument::OPTIONAL, 'The number of days a cart can remain inactive', "2")
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
            ->setHelp('This command allows you to remove carts that have been inactive for a defined period...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = (int)$input->getArgument('days');

        if ($days <= 0) {
            $io->error('The number of days should be greater than 0.');
            return 0;
        }

        $limitDate = new \DateTime("- $days days");
        $expiredCartsCount = 0;

        while ($carts = $this->userCommandRepository->findCartsNotModifiedSince($limitDate)) {
            foreach ($carts as $cart) {
                $this->manager->remove($cart);
            }

            $this->manager->flush();
            $this->manager->clear();

            $expiredCartsCount += count($cart);
        }


        if ($expiredCartsCount) {
            $io->success("$expiredCartsCount cart(s) have been deleted.");
        } else {
            $io->note('No expired carts.');
        }

        return 0;
    }
}

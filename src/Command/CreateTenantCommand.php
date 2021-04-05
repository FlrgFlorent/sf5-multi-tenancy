<?php

namespace App\Command;

use App\Entity\Main\Tenant;
use App\Messenger\Message\BuildTenantMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateTenantCommand extends Command
{

	private EntityManagerInterface $entityManager;

	private MessageBusInterface $bus;


	/**
	 * CreateTenantCommand constructor.
	 * @param EntityManagerInterface $entityManager
	 * @param MessageBusInterface $bus
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		MessageBusInterface $bus
	)
	{
		$this->entityManager = $entityManager;
		$this->bus = $bus;

		parent::__construct();
	}


	// the name of the command (the part after "bin/console")
	protected static $defaultName = 'app:create-tenant';


	protected function configure()
	{
		$this
			->setDescription('Creates a new tenant.')
			->setHelp('This command allows you to create a tenant with it database ...')
			->addArgument('tenantName', InputArgument::REQUIRED, 'Tenant Name')
		;
	}


	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 */
	protected function execute(
		InputInterface $input,
		OutputInterface $output
	): int
	{

		// outputs multiple lines to the console (adding "\n" at the end of each line)
		$output->writeln([
			'Tenant Creator',
			'============',
			'',
		]);

		$tenantName = (string) $input->getArgument('tenantName');

		$tenant = new Tenant();
		$tenant->setName($tenantName);

		$this->entityManager->persist($tenant);
		$this->entityManager->flush();

		$this->bus->dispatch(new BuildTenantMessage($tenant));

		return Command::SUCCESS;
	}
}
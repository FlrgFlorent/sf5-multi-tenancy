<?php

namespace App\Listener\Command;

use App\Doctrine\DBAL\TenantConnection;
use App\Entity\Main\Tenant;
use App\Repository\Main\TenantRepository;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class CommandListener
 * @package App\Listener\Command
 */
class CommandListener
{
	private array $allowedCommands;

	private TenantConnection $tenantConnection;

	private TenantRepository $tenantRepository;

	/**
	 * CommandListener constructor.
	 *
	 * @param TenantConnection $tenantConnection
	 * @param TenantRepository $tenantRepository
	 * @param array $allowedCommands
	 */
	public function __construct(
		TenantConnection $tenantConnection,
		TenantRepository $tenantRepository,
		$allowedCommands = []
	)
	{
		$this->tenantConnection = $tenantConnection;
		$this->tenantRepository = $tenantRepository;
		$this->allowedCommands = $allowedCommands;
	}

	/**
	 * @param ConsoleCommandEvent $event
	 * @throws Exception
	 */
	public function onConsoleCommand(ConsoleCommandEvent $event)
	{
		$command = $event->getCommand();
		$input = $event->getInput();

		if (!$this->isProperCommand($command)) {
			return;
		}

		$command->addArgument('tenant', InputArgument::OPTIONAL, 'Tenant ID');

		if (!$command->getDefinition()->hasOption('em')) {
			$command->getDefinition()->addOption(
				new InputOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command')
			);
		}

		$input->bind($command->getDefinition());

		if (is_null($input->getArgument('tenant')) || strpos($input->getArgument('tenant'), 'tenant=') === false) {
			return;
		}

		$tenantID = (int) str_replace('tenant=', '', $input->getArgument('tenant'));


		/** @var Tenant $tenant */
		$tenant = $this->tenantRepository->find($tenantID);

		if (!$tenant instanceof Tenant) {
			throw new Exception(sprintf('Tenant id° %s does not exists', $tenantID));
		}

		$input->setOption('em', 'tenant');
		$command->getDefinition()->getOption('em')->setDefault('tenant');

		$this->tenantConnection->changeParams($tenant->getDbName(), $tenant->getDbUser(), $tenant->getDbPassword());
	}

	/**
	 * @param Command $command
	 * @return bool
	 */
	private function isProperCommand(
		Command $command
	): bool
	{
		return in_array($command->getName(), $this->allowedCommands);
	}
}
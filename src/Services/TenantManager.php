<?php

namespace App\Services;

use App\Entity\Main\Tenant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class TenantManager
 * @package App\Services
 */
class TenantManager
{

	private EntityManagerInterface $entityManager;

	private KernelInterface $kernel;

	private array $database;

	/**
	 * TenantManager constructor.
	 * @param KernelInterface $kernel
	 * @param ParameterBagInterface $params
	 * @param EntityManagerInterface $entityManager
	 */
	public function __construct(
		KernelInterface $kernel,
		ParameterBagInterface $params,
		EntityManagerInterface $entityManager
	)
	{
		$this->kernel = $kernel;
		$this->entityManager = $entityManager;
		$this->database = [];
		$this->database['host'] = $params->get('TENANT_DATABASE_HOST');
		$this->database['port'] = $params->get('TENANT_DATABASE_PORT');
		$this->database['user'] = $params->get('TENANT_DATABASE_BUILDUSER');
		$this->database['password'] = $params->get('TENANT_DATABASE_BUILDPASSWORD');
	}

	/**
	 * @param Tenant $tenant
	 * @throws \Exception
	 */
	public function build(
		Tenant $tenant
	)
	{
		$this->entityManager->getConnection()->beginTransaction(); // suspend auto-commit

		try {
			$dbUser = 'tenant' . $tenant->getId() . '_' . strtolower(Password::generate());
			$dbPassword = Password::generate(32);
			$dbName = 'tenant' . $tenant->getId() . '_' . strtolower(Password::generate());

			$tenant->setDbName($dbName);
			$tenant->setDbUser($dbUser);
			$tenant->setDbPassword($dbPassword);

			$conn = mysqli_connect($this->database['host'], $this->database['user'], $this->database['password'], null, $this->database['port']);
			mysqli_query($conn, "CREATE DATABASE " . $dbName . ";");
			mysqli_query($conn, "CREATE USER '" . $dbUser . "'@'%' IDENTIFIED BY '" . $dbPassword . "';");
			mysqli_query($conn, "GRANT ALL PRIVILEGES ON " . $dbName . ".* TO '" . $dbUser . "'@'%';");
			mysqli_query($conn, "FLUSH PRIVILEGES;");
			mysqli_close($conn);

			$application = new Application($this->kernel);
			$application->setAutoExit(false);

			$input = new ArrayInput([
				'command' => 'doctrine:schema:create',
				'tenant' => 'tenant='.$tenant->getId()
			]);
			$output = new NullOutput();
			$application->run($input, $output);


			// PLAY HERE MIGRATIONS ;)

			$this->entityManager->persist($tenant);
			$this->entityManager->getConnection()->commit();
			$this->entityManager->flush();

		} catch (\Exception $e) {

			$this->entityManager->getConnection()->rollback();
			$this->entityManager->close();

		}
	}
}
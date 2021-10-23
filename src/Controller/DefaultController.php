<?php

namespace App\Controller;

use App\Doctrine\DBAL\TenantConnection;
use App\Entity\Main\Tenant;
use App\Entity\Tenant\User;
use App\Repository\Main\TenantRepository;
use App\Repository\Tenant\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
	/**
	 * @Route("/")
	 */
	public function index(
		Request $request,
		TenantRepository $tenantRepository,
		UserRepository $userRepository
	)
	{
		$tenant_id = $request->query->get('tenant_id');
		if(!$tenant_id) {
			throw $this->createNotFoundException('The tenant_id parameters is required in the URL');
		}
		/* @var $tenant Tenant */
		$tenant = $tenantRepository->findOneBy([
			'id' => $tenant_id
		]);
		if(!$tenant instanceof Tenant) {
			throw $this->createNotFoundException('The tenant for this ID not found');
		}
		/* @var $connection TenantConnection */
		$connection = $this->get('doctrine')->getConnection('tenant');
		$connection->changeParams($tenant->getDbName(), $tenant->getDbUser(), $tenant->getDbPassword());
		$connection->reconnect();

		return $this->render('default/index.html.twig', [
			'users' => $userRepository->findAll(),
			'tenant' => $tenant
		]);
	}
}
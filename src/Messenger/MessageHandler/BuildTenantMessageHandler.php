<?php

namespace App\Messenger\MessageHandler;

use App\Messenger\Message\BuildTenantMessage;
use App\Services\TenantManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Class BuildTenantMessageHandler
 * @package App\Messenger\MessageHandler
 */
class BuildTenantMessageHandler implements MessageHandlerInterface
{

	private TenantManager $tenantManager;

	/**
	 * BuildTenantMessageHandler constructor.
	 * @param TenantManager $tenantManager
	 */
	public function __construct(
		TenantManager $tenantManager
	)
	{
		$this->tenantManager = $tenantManager;
	}

    /**
     * @param BuildTenantMessage $buildTenantMessage
     * @throws \Exception
     */
	public function __invoke(
		BuildTenantMessage $buildTenantMessage
	)
	{
		$tenant = $buildTenantMessage->getTenant();

		$this->tenantManager->build($tenant);
	}
}
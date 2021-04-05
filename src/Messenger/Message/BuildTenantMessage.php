<?php

namespace App\Messenger\Message;

use App\Entity\Main\Tenant;

/**
 * Allows the creation of the database etc for the tenant
 *
 * Class BuildTenantMessage
 * @package App\Message
 */
class BuildTenantMessage
{
	private Tenant $tenant;

	/**
	 * BuildTenantMessage constructor.
	 * @param Tenant $tenant
	 */
	public function __construct(
		Tenant $tenant
	)
	{
		$this->tenant = $tenant;
	}

	/**
	 * @return Tenant
	 */
	public function getTenant()
	{
		return $this->tenant;
	}
}
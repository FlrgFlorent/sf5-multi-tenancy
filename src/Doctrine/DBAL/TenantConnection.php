<?php

namespace App\Doctrine\DBAL;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Event;
use Doctrine\DBAL\Exception;

/**
 * Class TenantConnection
 * @package App\Doctrine\DBAL
 */
class TenantConnection extends Connection
{
    /** @var mixed */
    protected $params = [];

    protected bool $isConnected = false;

    protected bool $autoCommit = true;

	/**
	 * CompanyConnection constructor.
	 *
	 * @param array $params
	 * @param Driver $driver
	 * @param Configuration|null $config
	 * @param EventManager|null $eventManager
	 * @throws Exception
	 */
    public function __construct(
		array $params,
		Driver $driver,
        ?Configuration $config = null,
        ?EventManager $eventManager = null
    )
    {
        $this->params = $params;
        parent::__construct($params, $driver, $config, $eventManager);
    }

    /**
     * @return bool
     */
    public function connect()
    {
        if ($this->isConnected) {
            return false;
        }

        $driverOptions = $this->params['driverOptions'] ?? [];
        $user = $this->params['user'] ?? null;
        $password = $this->params['password'] ?? null;

        $this->_conn = $this->_driver->connect($this->params, $user, $password, $driverOptions);
        $this->isConnected = true;

        if ($this->autoCommit === false) {
            $this->beginTransaction();
        }

        if ($this->_eventManager->hasListeners(Events::postConnect)) {
            $eventArgs = new Event\ConnectionEventArgs($this);
            $this->_eventManager->dispatchEvent(Events::postConnect, $eventArgs);
        }

        return true;
    }

    /**
     * @param string $dbName
     * @param string $dbUser
     * @param string $dbPassword
     */
    public function changeParams(
        string $dbName,
        string $dbUser,
        string $dbPassword
    )
    {
        $this->params['dbname'] = $dbName;
        $this->params['user'] = $dbUser;
        $this->params['password'] = $dbPassword;
    }


    public function reconnect()
    {
        if ($this->isConnected) {
            $this->close();
        }

        $this->connect();
    }

    /**
     * @return mixed|mixed[]
     */
    public function getParams(
	): array
	{
        return $this->params;
    }

    public function close(): void
    {
        $this->_conn = null;

        $this->isConnected = false;
    }
}
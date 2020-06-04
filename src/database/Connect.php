<?php


namespace Core\Modules\Database;

use Core\Modules\Config;
use PDO;

class Connect {

	/**
	 * Load configs
	 * @var Config
	 */
	private static Config $configs;

	private static $instance = null;

	private function __construct()
	{
		self::$configs = new Config();

		switch (self::$configs->connect) {

			case 'mysql':

				$mysqlConfigs = self::$configs->connections[self::$configs->connect];

				self::$instance = new PDO(
					'mysql:host=' . $mysqlConfigs['host'] . ';dbname=' . $mysqlConfigs['database'],
					$mysqlConfigs['user'],
					$mysqlConfigs['password']
				);

				break;
		}

	}

	public static function get()
	{
		if (static::$instance === null) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __clone() {}

	private function __wakeup() {}

}
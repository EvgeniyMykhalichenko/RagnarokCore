<?php


namespace Core\Modules\Di;


use Core\Modules\Config\ConfigExtension;

/**
 * Class DI
 * Dependency injection
 *
 * @package Core
 * @author  Krepysh <mykhalichenkoEvgeniy@gmail.com>
 */
class DI {

	private array $modules = [];

	protected function register(string $moduleName, callable $callback): void
	{
		$this->modules[$moduleName] = $callback();
	}

	public function __get(string $moduleName)
	{
		return $this->modules[$moduleName] ??= $this->modules[$moduleName];
	}

	protected function getModule(string $moduleName)
	{
		if(!array_key_exists($moduleName, $this->modules)) {
			throw new \Exception('Not found');
		}

		return $this->modules[$moduleName];
	}

	protected function getModules(): array
	{
		return $this->modules;
	}
}
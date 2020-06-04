<?php


namespace Core\Modules\Extension;


use Core\Application;
use Core\Modules\Extension\Interfaces\ExtensionInterface;

class Extension implements ExtensionInterface {

	public Application $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	public function register() {}
}
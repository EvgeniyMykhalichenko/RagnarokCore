<?php


namespace Core\Modules\Config;

use Core\Modules\Extension\Extension;

class ConfigExtension extends Extension {

	public function register()
	{
		return new Config($this->app);
	}

}
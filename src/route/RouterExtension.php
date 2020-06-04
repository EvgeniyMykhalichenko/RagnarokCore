<?php


namespace Core\Modules\Route;

use Core\Modules\Extension\Extension;

class RouterExtension extends Extension {

	public function register(): Router
	{
		return new Router(new RouteStorage($this->app));
	}

}
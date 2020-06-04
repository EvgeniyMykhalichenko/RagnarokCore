<?php


namespace Core\Modules\Database;


use Core\Modules\Config;

class Model extends Sparrow {

	public function __construct()
	{
		$this->setDb([
			'type' => 'pdomysql',
			'hostname' => 'mysql',
			'username' => 'root',
			'password' => 'root',
 			'database' => 'test'
		]);
	}

}
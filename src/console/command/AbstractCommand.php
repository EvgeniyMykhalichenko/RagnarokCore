<?php


namespace Core\Modules\Console\Command;

use Core\Modules\Console\Command\Interfaces\CommandInterface;

abstract class AbstractCommand implements CommandInterface {

	public string $commandAction = '';

	public string $commandDescription = '';

}
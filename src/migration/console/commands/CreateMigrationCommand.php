<?php


namespace Core\Modules\Migration\Commands;


use Core\Modules\Console\Command\Command;

class CreateMigrationCommand extends Command {

	public string $commandAction = 'migration::create';

	public string $commandDescription = 'Create new database migration';

	public function execute(): void
	{
		parent::execute();
	}

}
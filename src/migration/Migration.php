<?php

namespace Core\Modules\Database;

use Core\Exceptions\RouterException;
use Core\Modules\Console\Console;

class Migration {

	const MIGRATION_UP = 'up';
	const MIGRATION_DOWN = 'down';

	private $config;

	private $cdb;
	private $sql;

  private $listMigration;

	public function __construct(){
		$this->connection();
        $this->CheckORCreateServiceTable();
	}

	public function printCommands()
	{
		Console::log('--------------Миграции--------------', 'blue');
		$this->commandsList();
		Console::log('-------------------------------------', 'blue');
	}

	public function commandsList() {
		Console::log('> create [name_migration]   -   создаст файл миграции', 'green');
		Console::log('> run                       -   выполнит все миграции', 'green');
		Console::log('> run [name_migration]      -   выполнит одну миграцию', 'green');
		Console::log('> down                      -   откатит все миграции', 'green');
		Console::log('> down [name_migration]     -   откатит одну миграцию', 'green');
		Console::log('> list                      -   покажет сипоск миграций', 'green');
	}

	/**
	* Подключение к базе данных
	*/
	public function connection(){
		$dsn = "mysql:host=127.0.0.1;dbname=test;port=8989";
		$options = [
			\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
			\PDO::ATTR_EMULATE_PREPARES   => false,
		];
		try{

			$this->cdb = new \PDO($dsn, 'root', 'root', $options);
			$this->cdb->exec("USE test;");
		}
		catch(\PDOException $e){
			$this->errorLog($e->getMessage(), __LINE__, __FILE__, __FUNCTION__);
		}
	}

	/**
	* Запись ошибок
	*/
	public function errorLog($error, $line="", $file="", $function=""){
		echo "\033[31m"; //цвет вывода в консоль
		echo "\n\r DB Error at Line ".$line." in function ".$function."\n\r";
		echo "\033[0m";
		echo $error;
		echo "\n\r";
		echo "\033[0m";
	}

	protected function sql_exec(){
		if(strlen($this->sql)<2){
			$this->errorLog("Пустой запрос", __LINE__, __FILE__, __FUNCTION__);
			return false;
		}
		try{
			$this->cdb->exec($this->sql);
		}
		catch(PDOException $e){
			$this->errorLog($e->getMessage(), __LINE__, __FILE__, __FUNCTION__);
		}
	}


	public function createMigration(string $name){

		if (empty($name)) {
			Console::log('Migration name empty. Set migration name', 'red');
			die();
		}

		$name = 'migrate_'. date("d_m_Y_h_i_s", time()) . '_' . $name;

		$file = fopen("./../app/database/migrations/{$name}.php", 'w');

		if(!$file) {
			die('Error create file');
		}

		$str = '<?php
		namespace App\Database\Migration;
		
		use Core\Modules\Database\Migration;
		
		class '.$name.' extends Migration{
			public function up(){
				$this->addSql("");
			}

			public function down(){
				$this->addSql("");
			}
		}';

		Console::log("Migration {$name} created success", 'green');
		Console::bell(2);
		if(!fwrite($file, $str)){
			die('Error write file');
		}
	}

	/**
	* Добавление sql запроса
	*/
	public function addSql($sql){
		$this->sql = $sql;
	}

	/**
	* Hаходит все файлы с миграциями
	*/
	protected function searchMigration(){
		$list_file = scandir('./../app/database/migrations');
		foreach ($list_file as $k=>$file) {
			if($file == '.' || $file == '..' || in_array($file, $this->listMigration)){
				unset($list_file[$k]);
			}
		}
		return $list_file;
	}

	/**
	* Выполнить все миграции или Откатить
	*/
	protected function AllMigrationExec($type = 'up'){
		$list = $this->searchMigration();
		foreach ($list as $migration) {
			$this->execMigration($migration, $type);
		}
	}

	/**
	* Выполнить все миграции
	*/
	public function UpAllMigration(){
		$this->AllMigrationExec('up');
	}

	/**
	* Откатить все миграции
	*/
	public function DownAllMigration(){
		$this->AllMigrationExec('down');
	}

	/**
	* Выполнить одну миграцию
	*/
	public function UpOneMigration($name){
		$this->execMigration($name, 'up');
	}

	/**
	* Откатить одну миграцию
	*/
	public function DownOneMigration($name){
		$this->execMigration($name, 'down');
	}

	/**
	* Выполнение миграции
	*/
	protected function execMigration($fileName, $type='up'){

		$className = str_replace('.php', '', $fileName);

		$controllerPath = "App\Database\Migrations\\" . ucfirst($className);

		$m = new $controllerPath;
		dd($m);
		switch ($type) {
			case 'up':
				$m->up();
				$m->sql_exec();
        $this->addExecMigration($fileName);
				break;
			
			case 'down':
				$m->down();
				$m->sql_exec();
				break;
		}
	}

	public function printListMigration(){
		$list = $this->searchMigration();
		foreach ($list as $migration) {
			echo $migration."\n\r";
		}
	}

	public function up(){}

	public function down(){}

  /**
   * Проверяет наличие таблицы с миграциями, если таблицв нет создаёт
   */
  private function CheckORCreateServiceTable(){
    $sql = "CREATE TABLE IF NOT EXISTS `migrationTable` (
			  `migrationId` int(11) NOT NULL,
			  `name` varchar(512) NOT NULL,
			  `status` varchar(256) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=cp1251;";
    $this->cdb->exec( $sql );
    $sql = "SELECT `name`, `status` FROM `migrationTable`";
    $this->listMigration = array();
    foreach($this->cdb->query($sql) as $row) {
      $this->listMigration[] = $row['name'];
    }
  }

  /**
   * Добавляет выполненую миграцию в список выполненых
   */
  private function addExecMigration($name){
    $sql = "INSERT INTO `migrationTable`(`name`, `status`) VALUES ('".$name."', 'exec')";
    $this->cdb->exec( $sql );
  }
}

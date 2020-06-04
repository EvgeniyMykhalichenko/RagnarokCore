<?php


namespace Core\Modules\Extension\Interfaces;


use Core\Application;

interface ExtensionInterface {

	public function __construct(Application $application);

	public function register();

}
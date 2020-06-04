<?php

namespace Core\Modules\Http;


class Request {

	public const GET    = 'GET';
	public const POST   = 'POST';
	public const DELETE = 'DELETE';
	public const PUT    = 'PUT';

	private array $storage;

	private array $server;

	public function __construct()
	{
	   $this->storage =  $this->cleanInput($_REQUEST);
//		if (!empty($_REQUEST) ) {
//			$this->storage =  $this->cleanInput($_REQUEST);
//		} else {
//			$this->storage = $this->cleanInput(json_decode(file_get_contents("php://input"), true));
//		}

		$this->server = $_SERVER;
	}

	public function __get($name)
	{
		return $this->storage[$name] ??= $this->storage[$name];
	}

	public function isMethod(string $method): bool
	{
		return $this->getMethod() === $method;
	}

	public function getMethod(): string
	{
		return $this->server['REQUEST_METHOD'];
	}

	public function getURL(): string
	{
		return $this->server['REQUEST_URI'];
	}

	public function getClearURL(): string
	{
		if (($pos = strpos($this->getURL(), '?')) !== false) {
			return substr($this->getURL(), 0, $pos);
		}

		return $this->getURL();
	}

	public function getUserAgent(): string
	{
		return $this->server['HTTP_USER_AGENT'];
	}

	private function cleanInput($data)
	{
		if (is_array($data))
		{
			$cleaned = [];
			foreach ($data as $key => $value)
			{
				$cleaned[$key] = $this->cleanInput($value);
			}

			return $cleaned;
		}

		return trim(htmlspecialchars($data, ENT_QUOTES));
	}

	public function all()
	{
		return $this->storage;
	}
}
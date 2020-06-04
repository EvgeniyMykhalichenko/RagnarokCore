<?php


namespace Core\Modules\Http;

class Preparation {

	protected array $storage;

	public function __get($name)
	{
		if (isset($this->storage[$name])) return $this->storage[$name];
	}

	public static function one(array $object = []): array
	{
		$obj = new static();
		$obj->storage = $object;

		return $obj->toBeautify();
	}

	public static function many(array $collection = []): array
	{
		$data = [];

		foreach ($collection as $item)
		{
			$obj = new static();
			$obj->storage = $item;
			$data[] = $obj->toBeautify();
		}

		return $data;
	}

	protected function toBeautify(): array
	{
		return [];
	}
}
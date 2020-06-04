<?php

namespace Core\Modules\Route;

use Core\Modules\Http\Request;
use Core\Exceptions\RouterException;

class Route
{
	private array $collection = [];

	private string $url;

	private string $method;

	private array $parameters = [];

	private string $action;

	private $parametersByName;

	private $filters;

	private string $actionDelimiter = '@';

	/**
	 * @param string $url
	 * @param string $action
	 * @param string $method
	 *
	 * @throws \Exception
	 */
	private function add(string $url, $action, string $method)
	{
		if (!is_callable($action) && !strpos($action, $this->actionDelimiter)) {
			throw new RouterException("Invalid route action");
		}

		$route = new self();
		$route->setUrl($url);
		$route->setAction($action);
		$route->method = $method;

		$this->collection[] = $route;
	}

	/**
	 * Get routes collections
	 * @return array
	 */
	public function getCollection(): array
	{
		return $this->collection;
	}

	private function setUrl($url): void
	{
		$url = (string)$url;

		// make sure that the URL is suffixed with a forward slash
		if (substr($url, -1) !== '/') {
			$url .= '/';
		}

		$this->url = $url;
	}

	private function setAction(string $action):void
	{
		$this->action = $action;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	/**
	 * @param string $url
	 * @param string $action
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function get(string $url, string $action)
	{
		$this->add($url, $action, Request::GET);

		return $this;
	}

	/**
	 * @param string $url
	 * @param string $action
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function post(string $url, string $action)
	{
		$this->add($url, $action, Request::POST);

		return $this;
	}

	/**
	 * @param string $url
	 * @param string $action
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function put(string $url, string $action)
	{
		$this->add($url, $action, Request::PUT);

		return $this;
	}

	/**
	 * @param string $url
	 * @param string $action
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function delete(string $url, string $action)
	{
		$this->add($url, $action, Request::DELETE);

		return $this;
	}

	public function setFilters(array $filters, $parametersByName = false)
	{
		$this->filters          = $filters;
		$this->parametersByName = $parametersByName;
	}

	public function getRegex()
	{
		return preg_replace_callback('/(:\w+)/', [&$this, 'substituteFilter'], $this->url);
	}

	private function substituteFilter($matches)
	{
		if (isset($matches[1], $this->filters[$matches[1]])) {
			return $this->filters[$matches[1]];
		}

		return '([\w\-%]+)';
	}

	public function getParameters()
	{
		return $this->parameters;
	}

	public function setParameters(array $parameters = []): void
	{
		$this->parameters = $parameters;
	}

	/**
	 * @return mixed
	 * @throws RouterException
	 */
	public function dispatch()
	{
		list($class, $method) = explode($this->actionDelimiter, $this->action);

		$controllerPath = "App\Controllers\\" . ucfirst($class);

		if (!class_exists($controllerPath)) {
			throw new RouterException("Controller {$controllerPath} not exist");
		}

		if (!method_exists($controllerPath, $method)) {
			throw new RouterException("Method {$method} not fount in {$controllerPath}");
		}

		return call_user_func_array([new $controllerPath(), $method], $this->parameters);
	}

}

<?php

use Core\Modules\Http\Response;

if (!function_exists('env'))
{
	function env(string $name, ?string $default = null): string
	{
		return getenv($name) ?? $default;
	}
}

if (!function_exists('response'))
{
	function response(): Response
	{
		return new Response();
	}
}

if (!function_exists('dd'))
{
	function dd($variable): void
	{
		var_dump($variable);
		die();
	}
}

if (!function_exists('http_error_404'))
{
	function http_error_404()
	{
		header('HTTP/1.0 404 Not Found');
		exit();
	}
}
<?php


namespace Core\Modules\Http;


class Response {

	public const STATUS_SUCCESS = 'success';
	public const STATUS_ERROR = 'error';

	public function json( $data = null, int $code = 200): string
	{
		header_remove();
		http_response_code($code);
		header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
		header('Content-Type: application/json');

		$status = [
			200 => '200 OK',
			400 => '400 Bad Request',
			422 => 'Unprocessable Entity',
			500 => '500 Internal Server Error'
		];

		header('Status: ' . $status[$code]);

		return json_encode([
			'status' => $code < 300 ? self::STATUS_SUCCESS : self::STATUS_ERROR,
			'data'   => $data,
		]);
	}
}
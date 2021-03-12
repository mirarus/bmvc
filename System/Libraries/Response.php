<?php

/**
 * Response
 *
 * @package System\Libraries
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.0
*/

class Response
{
	
	protected static $statusCodes = [
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => '(Unused)',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported'
	];

	static function setHeader($code)
	{
		header("HTTP/1.1 " . $code . " " . self::setStatusCode($code));
		header("Content-Type: application/json; charset=utf-8");
	}
	
	static function setStatusCode($code)
	{
		return http_response_code($code);
	}

	static function getStatusCode()
	{
		return http_response_code();
	}

	static function getStatusMessage($code=null)
	{
		if (is_null($code)) {
			return self::$statusCodes[self::getStatusCode()];
		}
		return self::$statusCodes[$code];
	}

	static function json($data=null, $code=200)
	{
		header_remove();
		self::setStatusCode($code);
		header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
		header('Content-type: application/json');
		header('Status: ' . self::$statusCodes[$code]);
		return json_encode(['status' => $code < 300, 'message' => $data]);
	}
}

# Initialize
new Response;
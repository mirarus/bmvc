<?php

/**
 * Log
 *
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.2
 */

namespace System;

class Log
{

	static function emergency($message)
	{
		self::write('emergency', $message);
	}

	static function alert($message)
	{
		self::write('alert', $message);
	}

	static function critical($message)
	{
		self::write('critical', $message);
	}

	static function error($message)
	{
		self::write('error', $message);
	}

	static function warning($message)
	{
		self::write('warning', $message);
	}

	static function notice($message)
	{
		self::write('notice', $message);
	}

	static function info($message)
	{
		self::write('info', $message);
	}

	static function debug($message)
	{
		self::write('debug', $message);
	}

	static private function write($level, $message)
	{
		if (is_array($message)) {
			$message = serialize($message);
		}
		self::save('[' . date('d.m.Y H:i:s') . '] - [' . $level . '] - [' . self::get_request_method() . '] -> ' . $message);
	}

	static private function save($text)
	{
		$file = 'Log_' . date('d.m.Y') . '.log';
		$file = fopen(SYSTEMDIR . '/Logs/' . $file, 'a');
		if (fwrite($file, $text . "\r\n") === false) {
			MError::title('Log Error!')::print('Failed to create log file.', 'Check the write permissions.');
		}
		fclose($file);
	}

	static private function get_request_method()
	{
		$method = @$_SERVER['REQUEST_METHOD'];
		if ($method == 'HEAD') {
			ob_start();
			$method = 'GET';
		} elseif ($method == 'POST') {
			if (function_exists('getallheaders')) {
				getallheaders();
			}
			$headers = [];
			foreach (@$_SERVER as $name => $value) {
				if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
					$headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
				}
			}
			if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
				$method = $headers['X-HTTP-Method-Override'];
			}
		}
		return $method;
	}
}

# Initialize - AutoInitialize
# new Log;
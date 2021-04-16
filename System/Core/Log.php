<?php

/**
 * Log
 *
 * Mirarus BMVC
 * @package BMVC\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.5
 */

namespace BMVC\Core;

class Log
{

	private static $instance;

	static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

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

	private static function write($level, $message)
	{
		if (is_array($message)) {
			$message = serialize($message);
		}
		self::save('[' . date('d.m.Y H:i:s') . '] - [' . $level . '] - [' . self::get_request_method() . '] -> ' . $message);
	}

	private static function save($text)
	{
		$file = 'Log_' . date('d.m.Y') . '.log';
		$file = fopen(SYSTEMDIR . '/Logs/' . $file, 'a');
		if (fwrite($file, $text . "\r\n") === false) {
			MError::title('Log Error!')::print('Failed to create log file.', 'Check the write permissions.');
		}
		fclose($file);
	}

	private static function get_request_method()
	{
		$method = @$_SERVER['REQUEST_METHOD'];
		if ($method == "HEAD") {
			ob_start();
			$method = "GET";
		} elseif ($method == "POST") {
			if (function_exists('getallheaders'))
				getallheaders();
			$headers = [];
			foreach ($_SERVER as $name => $value) {
				if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
					$headers[@strtr(ucwords(strtolower(@strtr(substr($name, 5), ['_' => ' ']))), [' ' => '-', 'Http' => 'HTTP'])] = $value;
				}
			}
			if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], ["PUT", "DELETE", "PATCH"])) {
				$method = $headers['X-HTTP-Method-Override'];
			}
		}
		return $method;
	}

	function __call($method, $args)
	{
		return isset($this->{$method}) && is_callable($this->{$method}) ? call_user_func_array($this->{$method}, $args) : null;
	}

	static function __callStatic($method, $args)
	{
		return isset(self::$method) && is_callable(self::$method) ? call_user_func_array(self::$method, $args) : null;
	}

	function __set($key, $value)
	{
		$this->{$key} = $value instanceof \Closure ? $value->bindTo($this) : $value;
	}
}

# Initialize - AutoInitialize
# new Log;
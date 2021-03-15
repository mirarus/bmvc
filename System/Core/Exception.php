<?php

/**
 * Exception
 *
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.0
 */

namespace System;

class Exception
{

	private $error = [
		500                 => "Software Error",
		E_ERROR             => 'Error',
		E_WARNING           => 'Warning',
		E_PARSE             => 'Parsing Error',
		E_NOTICE            => 'Notice',
		E_CORE_ERROR        => 'Core Error',
		E_CORE_WARNING      => 'Core Warning',
		E_COMPILE_ERROR     => 'Compile Error',
		E_COMPILE_WARNING   => 'Compile Warning',
		E_USER_ERROR        => 'User Error',
		E_USER_WARNING      => 'User Warning',
		E_USER_NOTICE       => 'User Notice',
		E_STRICT            => 'Runtime Notice',
		E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
	];
	
	function __construct()
	{
		@ini_set("display_errors", 0);
		error_reporting(0);

		set_error_handler(array($this, 'error_handler'));
		register_shutdown_function(array($this, "shutdown_error_handler"));
	}

	function error_handler($number, $error, $file, $line)
	{
		$message_head = "Error [{$this->error[$number]}:{$number}]: {$error}";
		$message_body = "File: {$file}<br>Line: {$line}";
		$message_log  = $message_head . " - File: {$file} - Line: {$line}";

		Log::error($message_log);

		if ($number == 1 || $number == 4 || $number == 16 || $number == 64 || $number == 256 || $number == 4096) {
			MError::print($message_head, $message_body, null, 'danger');
		} elseif ($number == 2 || $number == 32 || $number == 128 || $number == 512) {
			MError::print($message_head, $message_body, null, 'warning');
		} elseif ($number == 8 || $number == 1024) {
			MError::print($message_head, $message_body, null, 'info');
		} elseif ($number == 8192 || $number == 16384) {
			MError::print($message_head, $message_body, null, 'success');
		} else {
			MError::print($message_head, $message_body);
		}
	}

	function shutdown_error_handler()
	{
		$error = error_get_last();
		if (isset($error['type'])) {
			switch ($error['type']) {
				case E_ERROR:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
				case E_RECOVERABLE_ERROR:
				case E_CORE_WARNING:
				case E_COMPILE_WARNING:
				case E_PARSE:
				header("HTTP/1.1 200 OK");
				$this->error_handler($error['type'], $error['message'], $error['file'], $error['line']);
			}
		}
	}
}

# Initialize
new Exception;
<?php

/**
 * MException
 *
 * Mirarus BMVC
 * @package System
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.3
 */

class MException
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
		$e_error = $this->error[$number] ? $this->error[$number] : 'Error';
		$e_title = "{$e_error} - [System Error]";
		$e_head  = "{$e_error} [{$number}]: {$error}";
		$e_body  = "File: {$file}<br>Line: {$line}";
		$e_log   = $e_head . " - File: {$file} - Line: {$line}";

		if (config('general/log') == true) {
			@System\Log::error($e_log);
		}

		if ($number == 1 || $number == 4 || $number == 16 || $number == 64 || $number == 256 || $number == 4096) {
			ep($e_head, $e_body, true, $e_title, 'danger');
		} elseif ($number == 2 || $number == 32 || $number == 128 || $number == 512) {
			ep($e_head, $e_body, true, $e_title, 'warning');
		} elseif ($number == 8 || $number == 1024) {
			ep($e_head, $e_body, true, $e_title, 'info');
		} elseif ($number == 8192 || $number == 16384) {
			ep($e_head, $e_body, true, $e_title, 'success');
		} else {
			ep($e_head, $e_body, true, $e_title);
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
new MException;
<?php

/**
 * Helper
 *
 * Mirarus BMVC
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.4
 */

namespace System;

class Helper
{
	
	private static $instance;

	function __construct()
	{		
		if (config('helpers') != null) {
			self::import(config('helpers'));
		}
	}

	static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private static function include($helper, $type=null)
	{
		if ($helper != null) {

			if ($helper == 'index') return false;

			$uri = ($type == 'SYSTEM') ? (SYSTEMDIR . '/Helpers/') : (APPDIR . '/Helpers/');
			if (_dir($uri)) {
				if (file_exists($file = $uri . $helper . '.php')) {
					require_once $file;
				} else {
					MError::title('Helpers Error!')::print('Helpers Not Found!', 'Helpers Name: ' . $helper);
				}
			} else {
				MError::title('Helpers Error!')::print('Helpers Dir Not Found!');
			}
		}
	}

	static function import($helper, $type='APP')
	{
		if ($helper != null) {
			if (is_array($helper)) {
				foreach($helper as $data) {
					self::include($data, $type);
				}
			} else {
				self::include($helper, $type);
			}
		}
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
# new Helper;
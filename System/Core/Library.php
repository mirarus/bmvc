<?php

/**
 * Library
 *
 * Mirarus BMVC
 * @package System\Core
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.7
 */

namespace System;

class Library
{
	
	private static $instance;

	function __construct()
	{		
		if (config('libraries') != null) {
			self::import(config('libraries'));
		}
	}

	static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private static function include($library, $type=null)
	{
		if ($library != null) {

			if ($library == 'index') return false;

			$uri = ($type == 'SYSTEM') ? (SYSTEMDIR . '/Libraries/') : (APPDIR . '/Libraries/');
			if (_dir($uri)) {
				if (file_exists($file = $uri . $library . '.php')) {
					require_once $file;
				} else {
					MError::title('Library Error!')::print('Library Not Found!', 'Library Name: ' . $library);
				}
			} else {
				MError::title('Library Error!')::print('Library Dir Not Found!');
			}
		}
	}

	static function import($library, $type='APP')
	{
		if ($library != null) {
			if (is_array($library)) {
				foreach($library as $data) {
					self::include($data, $type);
				}
			} else {
				self::include($library, $type);
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
# new Library;